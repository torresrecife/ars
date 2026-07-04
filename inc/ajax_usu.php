<?php

include 'seguranca.php';
protegePagina(0);

$regionService = new \App\Services\RegionService(
	new \App\Repositories\RegionRepository($conexao4)
);
$controller = new \App\Http\Controllers\UserAdminController(
	new \App\Services\UserAdminService(
		new \App\Repositories\UserAdminRepository($conexao4),
		$regionService
	),
	new \App\Support\View($app->basePath())
);

echo $controller->ajax($_POST);
