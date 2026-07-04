<?php

if (!defined('ARS_SEGURANCA_LOADED')) {
	include __DIR__ . '/inc/seguranca.php';
}

if (!defined('ARS_LEGACY_FUNCTIONS_LOADED')) {
	require_once __DIR__ . '/inc/functions.php';
}

protegePagina(0);

$view = new \App\Support\View($app->basePath());
$controller = new \App\Http\Controllers\RegionAdminController(
	new \App\Services\RegionAdminService(
		new \App\Repositories\RegionAdminRepository($conexao4)
	),
	$view
);

echo $controller->index();
