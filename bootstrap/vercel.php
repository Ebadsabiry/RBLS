<?php

// Vercel's deployed files are read-only. Only framework caches belong in /tmp.
$runtimeRoot = sys_get_temp_dir().'/rbls';
foreach (['bootstrap', 'storage/framework/cache/data', 'storage/framework/sessions', 'storage/framework/views', 'storage/logs'] as $directory) {
    $path = $runtimeRoot.'/'.$directory;
    if (! is_dir($path) && ! mkdir($path, 0700, true) && ! is_dir($path)) {
        throw new RuntimeException('Unable to create Laravel runtime directory.');
    }
}

$deploymentHost = $_ENV['VERCEL_URL'] ?? $_SERVER['VERCEL_URL'] ?? getenv('VERCEL_URL');
$defaults = [
    'APP_NAME' => 'Roman Bahram Logistic Services',
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'LARAVEL_STORAGE_PATH' => $runtimeRoot.'/storage',
    'APP_CONFIG_CACHE' => $runtimeRoot.'/bootstrap/config.php',
    'APP_EVENTS_CACHE' => $runtimeRoot.'/bootstrap/events.php',
    'APP_PACKAGES_CACHE' => $runtimeRoot.'/bootstrap/packages.php',
    'APP_ROUTES_CACHE' => $runtimeRoot.'/bootstrap/routes.php',
    'APP_SERVICES_CACHE' => $runtimeRoot.'/bootstrap/services.php',
    'VIEW_COMPILED_PATH' => $runtimeRoot.'/storage/framework/views',
    'LOG_CHANNEL' => 'stderr',
    'LOG_LEVEL' => 'warning',
    'SESSION_DRIVER' => 'cookie',
    'SESSION_SECURE_COOKIE' => 'true',
    'CACHE_STORE' => 'file',
    'QUEUE_CONNECTION' => 'sync',
    'INQUIRY_DELIVERY' => 'mail',
];
if ($deploymentHost) {
    $defaults['APP_URL'] = 'https://'.$deploymentHost;
}

// Dashboard environment variables always take priority over these defaults.
foreach ($defaults as $name => $value) {
    if (! isset($_ENV[$name]) && ! isset($_SERVER[$name]) && getenv($name) === false) {
        $_ENV[$name] = $_SERVER[$name] = $value;
        putenv($name.'='.$value);
    }
}
