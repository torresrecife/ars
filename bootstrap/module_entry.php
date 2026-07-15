<?php

use Illuminate\Http\Request;

if (!function_exists('ars_run_module_entry')) {
	function ars_module_public_path($section)
	{
		$map = array(
			'inicio' => 'index',
			'admin' => 'admin',
			'usuarios' => 'usuarios',
			'setores' => 'setores',
			'clientes' => 'clientes',
			'andamentos' => 'andamentos',
			'metas-select' => 'metas',
			'metas-admin' => 'metas',
			'metas' => 'metas',
			'semanas' => 'semanas',
			'regioes' => 'regioes',
			'carteiras' => 'carteiras',
			'painel' => 'painel',
			'producao' => 'producao',
			'relatorio-mensal' => 'relatorio',
			'relatorio-semanal' => 'relatorio',
			'relatorio' => 'relatorio',
		);

		$section = (string) $section;

		return isset($map[$section]) ? $map[$section] : ltrim($section, '/');
	}

	function ars_run_module_entry($section)
	{
		$requestMethod = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
		if ($requestMethod === 'GET' || $requestMethod === 'HEAD') {
			$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
			$directory = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
			$resolvedSection = '';
			if (is_callable($section)) {
				require __DIR__ . '/../vendor/autoload.php';
				$request = Request::capture();
				$resolvedSection = (string) call_user_func($section, $request);
			} else {
				$resolvedSection = (string) $section;
			}
			$target = ars_module_public_path($resolvedSection);
			if ($target !== null && $target !== '') {
				$location = ($directory === '' || $directory === '.')
					? '/' . ltrim($target, '/')
					: $directory . '/' . ltrim($target, '/');
				$query = (string) ($_SERVER['QUERY_STRING'] ?? '');
				if ($query !== '') {
					$location .= '?' . $query;
				}
				header('Location: ' . $location, true, 302);
				exit;
			}
		}

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
