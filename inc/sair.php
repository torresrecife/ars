<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$app = require dirname(__DIR__) . '/bootstrap/app.php';
$request = Illuminate\Http\Request::capture();
$app->instance('request', $request);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$authService = $app->make(App\Services\AuthService::class);
$authService->clearUserSession();

if (session_status() === PHP_SESSION_ACTIVE) {
	session_regenerate_id(true);
	session_destroy();
}

if (!headers_sent()) {
	header('Location: ../login.php');
	exit;
}

exit('<script>window.location="../login.php";</script>');
