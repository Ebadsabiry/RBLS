<?php

// Run in a separate process: php tests/vercel-smoke.php. No real mail is sent.
foreach ([
    'VERCEL' => '1',
    'VERCEL_URL' => 'rbls-preview.vercel.app',
    'APP_ENV' => 'testing',
    'APP_KEY' => 'base64:'.base64_encode(random_bytes(32)),
] as $name => $value) {
    $_ENV[$name] = $_SERVER[$name] = $value;
    putenv($name.'='.$value);
}

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();
$checks = 0;
function checkVercel(bool $condition, string $message): void
{
    global $checks;
    if (! $condition) {
        throw new RuntimeException($message);
    }
    $checks++;
    echo "PASS {$message}\n";
}
function requestVercel(string $path, string $method = 'GET', array $data = []): Symfony\Component\HttpFoundation\Response
{
    global $kernel;
    static $client = 1;
    $request = Illuminate\Http\Request::create('http://rbls-preview.vercel.app'.$path, $method, $data, [], [], [
        'REMOTE_ADDR' => '127.0.0.1',
        'HTTP_X_FORWARDED_FOR' => '198.51.100.'.($client++),
        'HTTP_X_FORWARDED_PROTO' => 'https',
        'HTTP_X_FORWARDED_PORT' => '443',
    ]);
    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);
    return $response;
}

checkVercel(str_starts_with(storage_path(), sys_get_temp_dir()), 'Serverless storage uses the temporary filesystem');
checkVercel(is_writable(config('view.compiled')), 'Compiled views have a writable directory');
checkVercel(str_starts_with($app->getCachedPackagesPath(), sys_get_temp_dir()), 'Package manifests use the temporary filesystem');
checkVercel(config('session.driver') === 'cookie' && config('session.secure'), 'Serverless sessions use secure cookies');
checkVercel(config('cache.default') === 'file', 'Rate limits persist between requests to an instance');
checkVercel(config('inquiries.delivery') === 'mail', 'Serverless inquiries require mail delivery');

foreach (['/', '/about', '/services', '/projects', '/company-profile', '/contact'] as $path) {
    $response = requestVercel($path);
    checkVercel($response->getStatusCode() === 200, $path.' renders with serverless configuration');
    checkVercel(str_contains($response->getContent(), 'https://rbls-preview.vercel.app/css/site.css'), $path.' respects forwarded HTTPS');
}

$data = ['name' => 'Deployment Test', 'email' => 'test@example.com', 'service' => 'Procurement', 'message' => 'Please deliver office supplies to our Kabul project.'];
$app['config']->set('mail.default', 'log');
$response = requestVercel('/contact', 'POST', $data);
checkVercel($response->getStatusCode() === 302 && $app['session.store']->has('errors'), 'Missing delivery configuration produces a form error');
checkVercel(! $app['session.store']->has('inquiry_status'), 'Missing delivery configuration never reports success');

$capture = new class {
    public array $messages = [];
    public function raw($body, $callback): void
    {
        $message = new Illuminate\Mail\Message(new Symfony\Component\Mime\Email);
        $callback($message);
        $this->messages[] = $message->getSymfonyMessage();
    }
};
Illuminate\Support\Facades\Mail::swap($capture);
$app['config']->set('mail.default', 'smtp');
$response = requestVercel('/contact', 'POST', $data);
checkVercel($response->getStatusCode() === 302 && str_contains($app['session.store']->get('inquiry_status', ''), 'has been sent'), 'Accepted email produces a success response');
checkVercel(count($capture->messages) === 1 && $capture->messages[0]->getTo()[0]->getAddress() === 'info@romanbahram.com', 'Email addresses the RBLS team');
checkVercel($capture->messages[0]->getReplyTo()[0]->getAddress() === $data['email'], 'Email replies go to the visitor');
checkVercel(! is_dir(storage_path('app/private/inquiries')), 'Serverless inquiries are never written to temporary storage');

Illuminate\Support\Facades\Mail::swap(new class {
    public function raw($body, $callback): void { throw new RuntimeException('Simulated SMTP failure'); }
});
requestVercel('/contact', 'POST', $data);
checkVercel($app['session.store']->has('errors') && ! $app['session.store']->has('inquiry_status'), 'SMTP failure never reports a lost inquiry as successful');

echo "\n{$checks} serverless checks passed.\n";
