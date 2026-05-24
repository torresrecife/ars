<?php

include("inc/seguranca.php");
protegePagina(0);

$view = new \App\Support\View($app->basePath());
$controller = new \App\Http\Controllers\FinancialDetailController(
	new \App\Services\NeoDetailService(new \App\Repositories\NeoDetailRepository(ars_sqlsrv_connection())),
	$view
);

echo $controller->index($_POST);
