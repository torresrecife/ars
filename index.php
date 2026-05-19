<?php

header('Cache-Control: no cache');
session_cache_limiter('private_no_expire');
session_cache_limiter('public');
date_default_timezone_set('America/Recife');
error_reporting(0);
ini_set('display_errors', '0');

include 'inc/seguranca.php';
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/inc/somadias.php';

protegePagina(0);

$view = new \App\Support\View(__DIR__);
$controller = new \App\Http\Controllers\HomeController(
	new \App\Services\MainPageService(
		new \App\Repositories\MainPageRepository($conexao4),
		$arrMonths
	),
	$view
);

echo $controller->index($_REQUEST, $_SESSION);
