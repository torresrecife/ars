<?php

require __DIR__ . '/inc/seguranca.php';

protegePagina(0);

if (!isset($_POST['flag']) || $_POST['flag'] !== 'U') {
	echo 2;
	exit;
}

$idUsuario = isset($_POST['id_usu']) ? (int) $_POST['id_usu'] : 0;
$novaSenha = isset($_POST['senha_usu1']) ? (string) $_POST['senha_usu1'] : '';

if ($idUsuario <= 0 || $novaSenha === '') {
	echo 2;
	exit;
}

if ((int) $_SESSION['usuarioID'] !== $idUsuario) {
	echo 2;
	exit;
}

echo ars_auth_service()->updatePasswordAndAccess($idUsuario, $novaSenha) ? 1 : 2;
