<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$onVercel = ($_ENV['VERCEL'] ?? $_SERVER['VERCEL'] ?? getenv('VERCEL')) === '1';
if ($onVercel) {
    require __DIR__.'/vercel.php';
}

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) use ($onVercel) {
        if ($onVercel) {
            $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_PORT);
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

if ($onVercel) {
    // Also permits checking the serverless bootstrap on Windows.
    $app->addAbsoluteCachePathPrefix(sys_get_temp_dir());
}

return $app;
