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

if (!isset($_SESSION['usuarioID']) || !isset($_SESSION['usuarioNome'])) {
    http_response_code(302);
    header('Location: /ars2/login.php');
    exit;
}

$controller = $app->make(App\Http\Controllers\SectorAdminController::class);
$response = $controller->webAjax($request);

$response->send();

$kernel->terminate($request, $response);
