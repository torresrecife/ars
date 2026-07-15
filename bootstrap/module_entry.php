<?php

use Illuminate\Http\Request;

if (!function_exists('ars_run_module_entry')) {
	function ars_run_module_entry($section)
	{
		define('LARAVEL_START', microtime(true));

		require __DIR__ . '/../vendor/autoload.php';

		$app = require_once __DIR__ . '/app.php';

		$request = Request::capture();
		$app->instance('request', $request);

		$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
		$kernel->bootstrap();

		$resolvedSection = is_callable($section) ? (string) call_user_func($section, $request) : (string) $section;

		$controller = $app->make(App\Http\Controllers\HomeController::class);
		$response = $controller->webSectionPage($request, $resolvedSection);

		$response->send();

		$kernel->terminate($request, $response);
	}
}
