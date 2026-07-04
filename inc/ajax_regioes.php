<?php

include 'seguranca.php';
protegePagina(0);

$controller = new \App\Http\Controllers\RegionAdminController(
	new \App\Services\RegionAdminService(
		new \App\Repositories\RegionAdminRepository($conexao4)
	),
	new \App\Support\View($app->basePath())
);

echo $controller->ajax($_POST);
