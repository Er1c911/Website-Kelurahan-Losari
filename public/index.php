<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (getenv('VERCEL')) {
    @mkdir('/tmp/bootstrap/cache', 0777, true);
    @mkdir('/tmp/framework/views', 0777, true);

    $runtimePaths = [
        'APP_CONFIG_CACHE' => '/tmp/bootstrap/cache/config.php',
        'APP_EVENTS_CACHE' => '/tmp/bootstrap/cache/events.php',
        'APP_PACKAGES_CACHE' => '/tmp/bootstrap/cache/packages.php',
        'APP_ROUTES_CACHE' => '/tmp/bootstrap/cache/routes.php',
        'APP_SERVICES_CACHE' => '/tmp/bootstrap/cache/services.php',
        'VIEW_COMPILED_PATH' => '/tmp/framework/views',
    ];

    foreach ($runtimePaths as $key => $value) {
        putenv($key.'='.$value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
