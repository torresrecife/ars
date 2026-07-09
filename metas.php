<?php

$view = new \App\Support\View($app->basePath());
$controller = new \App\Http\Controllers\MetaController(
	new \App\Services\MetaService(
		new \App\Repositories\MetaRepository($conexao4),
		new \App\Services\RegionService(new \App\Repositories\RegionRepository($conexao4))
	),
	$view
);

echo $controller->index($_POST, $_SESSION);
