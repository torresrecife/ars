<?php

declare(strict_types=1);

$app = require dirname(__DIR__) . '/bootstrap/legacy_app.php';
require_once $app->basePath('inc/functions.php');
$routes = require $app->basePath('routes/legacy_web.php');
$path = isset($_GET['page']) ? '/' . ltrim((string) $_GET['page'], '/') : '/';

if (!isset($routes[$path])) {
	header('Content-Type: text/plain; charset=utf-8');
	echo "ARS foundation bootstrap loaded.\n";
	echo "Base path: " . $app->basePath() . "\n";
	exit;
}

$route = $routes[$path];
$controllerClass = $route['controller'];
$action = $route['action'];
$connection = $app->db()->mysql();
$view = new \App\Support\View($app->basePath());
$sqlsrvConnection = null;
$regionService = new \App\Services\RegionService(
	new \App\Repositories\RegionRepository($connection)
);

try {
	$sqlsrvConnection = $app->db()->sqlsrv();
} catch (\RuntimeException $exception) {
	$sqlsrvConnection = null;
}

if ($controllerClass === \App\Http\Controllers\MetaController::class) {
	$controller = new $controllerClass(
		new \App\Services\MetaService(new \App\Repositories\MetaRepository($connection)),
		$view
	);
	echo $controller->$action($_REQUEST);
	exit;
}

if ($controllerClass === \App\Http\Controllers\WeekController::class) {
	$controller = new $controllerClass(
		new \App\Services\WeekService(new \App\Repositories\WeekRepository($connection)),
		$view
	);
	if ($action === 'ajax') {
		echo $controller->$action($_REQUEST);
		exit;
	}

	echo $controller->$action();
	exit;
}

if ($controllerClass === \App\Http\Controllers\FinancialDetailController::class) {
	$controller = new $controllerClass(
		new \App\Services\NeoDetailService(
			new \App\Repositories\NeoDetailRepository($sqlsrvConnection),
			new \App\Repositories\DashboardRepository($connection),
			$regionService
		),
		$view
	);
	echo $controller->$action($_REQUEST, $_SESSION);
	exit;
}

if ($controllerClass === \App\Http\Controllers\AndamentoDetailController::class) {
	$controller = new $controllerClass(
		new \App\Services\NeoDetailService(
			new \App\Repositories\NeoDetailRepository($sqlsrvConnection),
			new \App\Repositories\DashboardRepository($connection),
			$regionService
		),
		$view
	);
	echo $controller->$action($_REQUEST, $_SESSION);
	exit;
}

if ($controllerClass === \App\Http\Controllers\DashboardPanelController::class) {
	$controller = new $controllerClass(
		new \App\Services\DashboardPanelService(
			new \App\Repositories\DashboardRepository($connection),
			new \App\Repositories\NeoPanelRepository($sqlsrvConnection),
			$regionService,
			$arrMonths
		)
	);
	echo $controller->$action($_REQUEST, $_SESSION);
	exit;
}
