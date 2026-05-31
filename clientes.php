<?php

if (!defined('ARS_SEGURANCA_LOADED')) {
	include __DIR__ . '/inc/seguranca.php';
}

if (!defined('ARS_LEGACY_FUNCTIONS_LOADED')) {
	require_once __DIR__ . '/inc/functions.php';
}

protegePagina(0);

$view = new \App\Support\View($app->basePath());
$controller = new \App\Http\Controllers\ClientAdminController(
	new \App\Services\ClientAdminService(
		new \App\Repositories\ClientAdminRepository($conexao4, ars_sqlsrv_connection())
	),
	$view
);

echo $controller->index();
