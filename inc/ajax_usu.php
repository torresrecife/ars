<?php

include 'seguranca.php';
protegePagina(0);

$controller = new \App\Http\Controllers\UserAdminController(
	new \App\Services\UserAdminService(
		new \App\Repositories\UserAdminRepository($conexao4)
	),
	new \App\Support\View($app->basePath())
);

echo $controller->ajax($_POST);
