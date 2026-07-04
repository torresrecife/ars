<?php

if (!defined('ARS_SEGURANCA_LOADED')) {
	include __DIR__ . '/inc/seguranca.php';
}

if (!defined('ARS_LEGACY_FUNCTIONS_LOADED')) {
	require_once __DIR__ . '/inc/functions.php';
}

protegePagina(0);

$connectionMysql = isset($GLOBALS['conexao4']) ? $GLOBALS['conexao4'] : (isset($conexao4) ? $conexao4 : null);
$months = isset($GLOBALS['arrMonths']) ? $GLOBALS['arrMonths'] : (isset($arrMonths) ? $arrMonths : array());

$controller = new \App\Http\Controllers\DashboardPanelController(
	new \App\Services\DashboardPanelService(
		new \App\Repositories\DashboardRepository($connectionMysql),
		new \App\Repositories\NeoPanelRepository(ars_sqlsrv_connection()),
		new \App\Services\RegionService(
			new \App\Repositories\RegionRepository($connectionMysql)
		),
		$months
	)
);

echo $controller->index($_POST, $_SESSION);
