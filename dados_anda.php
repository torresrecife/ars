<?php

include("inc/seguranca.php");
protegePagina(0);

$view = new \App\Support\View($app->basePath());
$controller = new \App\Http\Controllers\AndamentoDetailController(
	new \App\Services\NeoDetailService(
		new \App\Repositories\NeoDetailRepository(ars_sqlsrv_connection()),
		new \App\Repositories\DashboardRepository($conexao4)
	),
	$view
);

echo $controller->index($_POST);
