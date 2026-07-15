<?php

define('LARAVEL_START', microtime(true));

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$request = Illuminate\Http\Request::capture();
$app->instance('request', $request);

$requestPath = (string) parse_url((string) $request->server('REQUEST_URI', ''), PHP_URL_PATH);
$isDirectIndexRequest = basename($requestPath) === 'index.php';

if ($isDirectIndexRequest) {
	$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
	$kernel->bootstrap();

	if (session_status() === PHP_SESSION_ACTIVE && session_name() !== 'PHPSESSID') {
		session_write_close();
	}

	if (session_name() !== 'PHPSESSID') {
		session_name('PHPSESSID');
	}

	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$authService = $app->make(App\Services\AuthService::class);
	if (!$authService->currentUser()) {
		header('Location: login.php', true, 302);
		exit;
	}

	$controller = $app->make(App\Http\Controllers\HomeController::class);
	$response = $controller->webIndex($request);
	$response->send();
	$kernel->terminate($request, $response);
	return;
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
