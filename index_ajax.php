<?php

include("inc/seguranca.php");
protegePagina(0);
require_once __DIR__ . "/inc/functions.php";

$controller = new \App\Http\Controllers\DashboardPanelController(
	new \App\Services\DashboardPanelService(
		new \App\Repositories\DashboardRepository($conexao4),
		new \App\Repositories\NeoPanelRepository($conexao1),
		$arrMonths
	)
);

echo $controller->index($_POST);
