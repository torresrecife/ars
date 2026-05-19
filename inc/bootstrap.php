<?php

declare(strict_types=1);

use App\Support\LegacyConfig;

if (!defined('ARS_BOOTSTRAP_LOADED')) {
	define('ARS_BOOTSTRAP_LOADED', true);

	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$app = require __DIR__ . '/../bootstrap/app.php';
	$arsConfig = LegacyConfig::build($app->config()->all());

	date_default_timezone_set($arsConfig['app']['timezone']);
	ini_set('display_errors', $arsConfig['app']['display_errors'] ? '1' : '0');
	error_reporting($arsConfig['app']['display_errors'] ? E_ALL : 0);

	$_SG = array(
		'conectaServidor' => true,
		'caseSensitive' => $arsConfig['auth']['case_sensitive'],
		'validaSempre' => $arsConfig['auth']['validate_always'],
		'paginaLogin' => $arsConfig['auth']['login_page'],
		'tabela' => $arsConfig['auth']['user_table'],
		'servidor' => $arsConfig['db']['mysql']['host'],
		'usuario' => $arsConfig['db']['mysql']['user'],
		'senha' => $arsConfig['db']['mysql']['pass'],
		'banco' => $arsConfig['db']['mysql']['name'],
	);

	$conexao4 = null;
	if ($_SG['conectaServidor'] == true) {
		$conexao4 = $app->db()->mysql();
		if (!$conexao4) {
			die("MySQL: Nao foi possivel conectar-se ao servidor [" . $arsConfig['db']['mysql']['host'] . "].");
		}
	}

	$waitStartMinute = (int) $arsConfig['maintenance']['neo_replica_wait_start_minute'];
	$waitEndMinute = (int) $arsConfig['maintenance']['neo_replica_wait_end_minute'];
	$currentMinute = (int) date('i');
	if ($waitEndMinute > $waitStartMinute && $currentMinute >= $waitStartMinute && $currentMinute < $waitEndMinute) {
		echo "<meta http-equiv='refresh' content='30'>";
		echo "<div align='center' style='margin-top:50px; height:50px;font-size:18pt'>";
		echo "Aguarde a atualizacao do sistema. Tente novamente em 2 minutos";
		echo "</div>";
		exit;
	}

	$conexao1 = null;
	if (function_exists('sqlsrv_connect')) {
		$conexao1 = $app->db()->sqlsrv();
	}
}
