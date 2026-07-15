<?php

use Illuminate\Http\Request;

if (!function_exists('ars_run_module_entry')) {
	function ars_run_module_entry($hidSend)
	{
		define('LARAVEL_START', microtime(true));

		require __DIR__ . '/../vendor/autoload.php';

		$app = require_once __DIR__ . '/app.php';

		$request = Request::capture();
		$app->instance('request', $request);

		$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
		$kernel->bootstrap();

		$resolvedHidSend = is_callable($hidSend) ? (int) call_user_func($hidSend, $request) : (int) $hidSend;

		$controller = $app->make(App\Http\Controllers\HomeController::class);
		$response = $controller->webStatePage($request, $resolvedHidSend);

		$response->send();

		$kernel->terminate($request, $response);
	}
}
