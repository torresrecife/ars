<?php

declare(strict_types=1);

$app = require dirname(__DIR__) . '/bootstrap/app.php';
require_once $app->basePath('inc/functions.php');
$routes = require $app->basePath('routes/web.php');
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
		new \App\Services\NeoDetailService(new \App\Repositories\NeoDetailRepository($app->db()->sqlsrv())),
		$view
	);
	echo $controller->$action($_REQUEST);
	exit;
}

if ($controllerClass === \App\Http\Controllers\AndamentoDetailController::class) {
	$controller = new $controllerClass(
		new \App\Services\NeoDetailService(new \App\Repositories\NeoDetailRepository($app->db()->sqlsrv())),
		$view
	);
	echo $controller->$action($_REQUEST);
	exit;
}

if ($controllerClass === \App\Http\Controllers\DashboardPanelController::class) {
	$controller = new $controllerClass(
		$connection,
		$app->db()->sqlsrv(),
		$arrMonths
	);
	echo $controller->$action($_REQUEST);
	exit;
}
