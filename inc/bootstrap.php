<?php

declare(strict_types=1);

use App\Support\LegacyConfig;

if (!defined('ARS_BOOTSTRAP_LOADED')) {
	define('ARS_BOOTSTRAP_LOADED', true);

	$app = require __DIR__ . '/../bootstrap/legacy_app.php';
	$arsConfig = LegacyConfig::build($app->config()->all());
	$GLOBALS['app'] = $app;

	date_default_timezone_set($arsConfig['app']['timezone']);
	ini_set('display_errors', $arsConfig['app']['display_errors'] ? '1' : '0');
	error_reporting($arsConfig['app']['display_errors'] ? E_ALL : 0);

	$_SG = array(
		'conectaServidor' => true,
		'caseSensitive' => $arsConfig['auth']['case_sensitive'],
		'validaSempre' => $arsConfig['auth']['validate_always'],
		'tabela' => $arsConfig['auth']['user_table'],
		'servidor' => $arsConfig['db']['mysql']['host'],
		'usuario' => $arsConfig['db']['mysql']['user'],
		'senha' => $arsConfig['db']['mysql']['pass'],
		'banco' => $arsConfig['db']['mysql']['name'],
	);
	$GLOBALS['_SG'] = $_SG;

	$conexao4 = null;
	if ($_SG['conectaServidor'] == true) {
		try {
			$conexao4 = $app->db()->mysql();
		} catch (\RuntimeException $exception) {
			echo "<div style='margin:40px;font-family:Arial,sans-serif;font-size:14px'>";
			echo "<b>Erro de configuracao do banco MySQL.</b><br><br>";
			echo htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8');
			echo "</div>";
			exit;
		}

		if (!$conexao4) {
			die("MySQL: Nao foi possivel conectar-se ao servidor [" . $arsConfig['db']['mysql']['host'] . "].");
		}
	}
	$GLOBALS['conexao4'] = $conexao4;

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
	$GLOBALS['conexao1'] = $conexao1;
}
