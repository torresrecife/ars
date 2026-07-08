<?php

include("seguranca.php");
protegePagina(0);

$view = new \App\Support\View($app->basePath());
$controller = new \App\Http\Controllers\MetaController(
	new \App\Services\MetaService(
		new \App\Repositories\MetaRepository($conexao4),
		new \App\Services\RegionService(new \App\Repositories\RegionRepository($conexao4))
	),
	$view
);

if (isset($_POST['flag']) && $_POST['flag'] == "E") {
	header("Content-Type: text/html; charset=ISO-8859-1", true);
}

echo $controller->ajax($_POST);
?>
