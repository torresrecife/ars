<?php

$view = new \App\Support\View($app->basePath());
$controller = new \App\Http\Controllers\WeekController(
	new \App\Services\WeekService(new \App\Repositories\WeekRepository($conexao4)),
	$view
);

echo $controller->index();
