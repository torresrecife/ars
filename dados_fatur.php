<?php

include("inc/seguranca.php");
protegePagina(0);

$view = new \App\Support\View($app->basePath());
$controller = new \App\Http\Controllers\FinancialDetailController(
	new \App\Services\NeoDetailService(new \App\Repositories\NeoDetailRepository($conexao1)),
	$view
);

echo $controller->index($_POST);
