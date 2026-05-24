<?php

include 'inc/seguranca.php';
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/inc/somadias.php';

protegePagina(0);

$view = new \App\Support\View(__DIR__);
$controller = new \App\Http\Controllers\GeneralProductionController(
	new \App\Services\GeneralProductionService(
		new \App\Repositories\GeneralProductionRepository($conexao4),
		new \App\Repositories\GeneralProductionNeoRepository(ars_sqlsrv_connection())
	),
	$view
);

echo $controller->weekly($_POST, $_SESSION);
