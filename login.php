<?php

define('LARAVEL_START', microtime(true));

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$request = Illuminate\Http\Request::capture();
$app->instance('request', $request);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_SESSION['usuarioID']) && isset($_SESSION['usuarioNome'])) {
    header('Location: index');
    exit;
}

$controller = $app->make(App\Http\Controllers\AuthController::class);
$response = $request->isMethod('post')
    ? $controller->login($request)
    : $controller->showLogin($request);

$response->send();

$kernel->terminate($request, $response);
