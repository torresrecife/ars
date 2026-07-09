<?php

define('LARAVEL_START', microtime(true));

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$request = Illuminate\Http\Request::capture();
$app->instance('request', $request);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$controller = $app->make(App\Http\Controllers\HomeController::class);
$response = $controller->webIndex($request);

$response->send();

$kernel->terminate($request, $response);
