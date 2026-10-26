<?php
// Run: php tests/website-smoke.php. No PHPUnit or database required.
putenv('APP_ENV=testing');
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();
$app['config']->set(['session.driver' => 'array', 'cache.default' => 'array', 'mail.default' => 'log']);
$testRoot = storage_path('framework/testing/site-'.bin2hex(random_bytes(6)));
$app['config']->set('filesystems.disks.local.root', $testRoot);
$checks = 0;
function check($condition, $message) { global $checks; if (!$condition) throw new RuntimeException($message); $checks++; echo "PASS {$message}\n"; }
function requestSite($path, $method = 'GET', $data = [], $ip = null) {
    global $kernel;
    static $counter = 1;
    $request = Illuminate\Http\Request::create('http://localhost'.$path, $method, $data, [], [], ['HTTP_ACCEPT' => $method === 'POST' ? 'application/json' : 'text/html', 'REMOTE_ADDR' => $ip ?? '198.51.100.'.($counter++)]);
    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);
    return $response;
}
try {
    foreach (['/', '/about', '/services', '/projects', '/company-profile', '/contact'] as $path) {
        $response = requestSite($path);
        check($response->getStatusCode() === 200, $path.' renders successfully');
        $html = $response->getContent();
        check(str_contains($html, 'css/site.css') && str_contains($html, 'js/site.js'), $path.' loads shared assets');
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        check($dom->getElementsByTagName('h1')->length === 1, $path.' has one primary heading');
        foreach ($dom->getElementsByTagName('img') as $image) {
            $file = public_path(rawurldecode(parse_url($image->getAttribute('src'), PHP_URL_PATH)));
            check(is_file($file), 'Image exists: '.basename($file));
        }
        if ($path === '/projects') check(substr_count($html, 'data-project data-client=') === 53, 'All 53 portfolio assignments render');
    }
    $data = ['name' => 'Test Partner', 'email' => 'partner@example.com', 'service' => 'Procurement', 'message' => 'Please arrange office supplies for our Kabul project.'];
    $response = requestSite('/contact', 'POST', $data);
    check($response->getStatusCode() === 302, 'Valid inquiry redirects after saving');
    $disk = Illuminate\Support\Facades\Storage::disk('local');
    $saved = $disk->files('inquiries');
    check(count($saved) === 1, 'Inquiry stored on private disk');
    $inquiry = json_decode($disk->get($saved[0]), true);
    check($inquiry['email'] === $data['email'] && isset($inquiry['reference']), 'Stored inquiry includes valid data and reference');
    foreach ([[], array_merge($data, ['service' => 'Invalid']), array_merge($data, ['email' => 'invalid']), array_merge($data, ['website' => 'spam']), array_merge($data, ['message' => 'short'])] as $invalid) {
        check(requestSite('/contact', 'POST', $invalid)->getStatusCode() === 422, 'Invalid inquiry rejected');
    }
    check(count($disk->files('inquiries')) === 1, 'Rejected inquiries are not saved');
    // Capture notification locally; no real mail leaves the application.
    $capture = new class {
        public array $messages = [];
        public function raw($body, $callback) { $message = new Illuminate\Mail\Message(new Symfony\Component\Mime\Email); $callback($message); $this->messages[] = $message->getSymfonyMessage(); }
    };
    Illuminate\Support\Facades\Mail::swap($capture);
    $app['config']->set('mail.default', 'smtp');
    check(requestSite('/contact', 'POST', $data)->getStatusCode() === 302, 'Configured notification flow succeeds');
    check(count($capture->messages) === 1 && $capture->messages[0]->getTo()[0]->getAddress() === 'info@romanbahram.com', 'Notification addresses RBLS');
    check($capture->messages[0]->getReplyTo()[0]->getAddress() === $data['email'], 'Notification replies to the inquirer');
    $app['config']->set('mail.default', 'log');
    for ($i = 0; $i < 5; $i++) requestSite('/contact', 'POST', [], '203.0.113.50');
    check(requestSite('/contact', 'POST', [], '203.0.113.50')->getStatusCode() === 429, 'Inquiry endpoint limits repeated submissions');
    check(is_file(public_path('RBLS Data/RBLS Profile 2026.pdf')), 'Company profile download exists');
    check(is_file(public_path('RBLS Data/RBLS Past Experince Portfoilio.pdf')), 'Portfolio download exists');
    echo "\n{$checks} checks passed.\n";
} finally {
    // Delete only test files created under this uniquely named test directory.
    if (is_dir($testRoot.'/inquiries')) { foreach (glob($testRoot.'/inquiries/*.json') as $file) unlink($file); rmdir($testRoot.'/inquiries'); }
    if (is_dir($testRoot)) rmdir($testRoot);
}
