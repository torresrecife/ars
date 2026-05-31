<?php

include 'seguranca.php';
protegePagina(0);

$controller = new \App\Http\Controllers\ClientAdminController(
	new \App\Services\ClientAdminService(
		new \App\Repositories\ClientAdminRepository($conexao4, ars_sqlsrv_connection())
	),
	new \App\Support\View($app->basePath())
);

echo $controller->ajax($_POST);
