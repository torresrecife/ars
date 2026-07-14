<?php

declare(strict_types=1);

if (defined('ARS_SEGURANCA_LOADED')) {
	return;
}

define('ARS_SEGURANCA_LOADED', true);

require_once __DIR__ . '/bootstrap.php';

function ars_auth_service() {
	static $service = null;
	global $_SG;

	if ($service instanceof \App\Services\AuthService) {
		return $service;
	}

	$legacyApp = isset($GLOBALS['app']) ? $GLOBALS['app'] : null;
	if (!$legacyApp || !method_exists($legacyApp, 'db')) {
		throw new \RuntimeException('Legacy application bootstrap is not available.');
	}

	$connection = $legacyApp->db()->mysql();
	$table = isset($_SG['tabela']) ? $_SG['tabela'] : 'usuarios';
	$repository = new \App\Repositories\UserRepository($connection, $table);
	$regionRepository = new \App\Repositories\RegionRepository($connection);
	$service = new \App\Services\AuthService($repository, $regionRepository);

	return $service;
}

function ars_hash_password($password) {
	return ars_auth_service()->hashPassword($password);
}

function ars_verify_password($password, $storedHash) {
	return ars_auth_service()->verifyPassword($password, $storedHash);
}

function ars_get_user_by_login($usuario, $conex) {
	return ars_auth_service()->findUserByLogin($usuario);
}

function ars_get_user_by_id($idUsuario, $conex) {
	return ars_auth_service()->findUserById($idUsuario);
}

function ars_store_user_session($resultado) {
	ars_auth_service()->storeUserSession($resultado);
}

function ars_clear_user_session() {
	ars_auth_service()->clearUserSession();
}

function ars_refresh_user_access($idUsuario, $conex) {
	return ars_auth_service()->refreshUserAccess($idUsuario);
}

function validaUsuario($usuario, $senha, $conex){
	return ars_auth_service()->attempt($usuario, $senha) !== false;
}

function protegePagina($valor = 0){
	global $conexao4;
	if (!isset($_SESSION['usuarioID']) || !isset($_SESSION['usuarioNome'])) {
		expulsaVisitante($valor);
	}

	$usuarioAtual = ars_get_user_by_id($_SESSION['usuarioID'], $conexao4);
	if (empty($usuarioAtual)) {
		expulsaVisitante($valor);
	}

	$_SESSION['usuarioNome'] = $usuarioAtual['nome_usu'];
	$_SESSION['usuarioNivel'] = $usuarioAtual['nivel_usu'];
	$_SESSION['usuarioST'] = $usuarioAtual['status_usu'];
	$_SESSION['usuarioSetor'] = $usuarioAtual['id_setor'];
	$_SESSION['usuarioCliente'] = $usuarioAtual['id_cliente'];
	$_SESSION['usuarioRegiaoModo'] = isset($usuarioAtual['regiao_modo']) ? (string) $usuarioAtual['regiao_modo'] : 'N';

	$regionRepository = new \App\Repositories\RegionRepository($conexao4);
	$_SESSION['usuarioRegiaoIds'] = implode(',', $regionRepository->listRegionIdsByUserId((int) $usuarioAtual['id_usu']));
	$_SESSION['usuarioRegiaoUfs'] = implode(',', $regionRepository->listUfCodesByUserId((int) $usuarioAtual['id_usu']));
}

function expulsaVisitante($valor) {
	ars_clear_user_session();

	if (session_status() === PHP_SESSION_ACTIVE) {
		session_regenerate_id(true);
		session_destroy();
	}

	$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
	$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
	$directory = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
	$loginPath = ($directory === '' || $directory === '.') ? '/login.php' : $directory . '/login.php';
	if ($valor == 1) {
		exit('<SCRIPT LANGUAGE="JavaScript">window.location="http://' . $host . $loginPath . '?alerta=1"</script>');
	}

	exit('<SCRIPT LANGUAGE="JavaScript">window.location="http://' . $host . $loginPath . '"</script>');
}
