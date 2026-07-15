<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$request = Illuminate\Http\Request::capture();
$app->instance('request', $request);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

$authService = $app->make(App\Services\AuthService::class);
$currentUser = $authService->currentUser();
if (empty($currentUser)) {
	http_response_code(302);
	header('Location: login.php');
	exit;
}

$authService->syncSessionContext($currentUser);

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

echo $authService->updatePasswordAndAccess($idUsuario, $novaSenha) ? 1 : 2;
