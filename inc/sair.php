<?php

include 'seguranca.php';

ars_clear_user_session();

if (session_status() === PHP_SESSION_ACTIVE) {
	session_regenerate_id(true);
	session_destroy();
}

if (!headers_sent()) {
	header('Location: ../login.php');
	exit;
}

exit('<script>window.location="../login.php";</script>');
