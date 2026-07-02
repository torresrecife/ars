<?php

include("seguranca.php");
protegePagina(0);

if ($_POST['flag'] == "E") {
	$anda_id = (int) $_POST['anda_id'];
	$query = mysqli_query($conexao4, "SELECT * FROM andamentos WHERE anda_id = " . $anda_id . " LIMIT 1");
	$row = $query ? mysqli_fetch_assoc($query) : false;
	if (!$row) {
		header('Content-Type: application/json; charset=UTF-8', true);
		echo json_encode(array(
			'anda_id' => '',
			'nome' => '',
			'chave' => '',
			'anda_neo' => '',
			'especie' => '',
			'painel' => '',
			'titulo' => '',
			'tipos' => array(),
		));
		exit;
	}

	$tipos = array();
	foreach (explode(',', isset($row['anda_neo']) ? (string) $row['anda_neo'] : '') as $tipo) {
		$tipo = trim($tipo);
		if ($tipo !== '') {
			$tipos[] = $tipo;
		}
	}

	header('Content-Type: application/json; charset=UTF-8', true);
	echo json_encode(array(
		'anda_id' => (int) $row['anda_id'],
		'nome' => (string) $row['nome'],
		'chave' => (string) $row['chave'],
		'anda_neo' => (string) $row['anda_neo'],
		'especie' => (string) $row['especie'],
		'painel' => (string) $row['painel'],
		'titulo' => (string) $row['titulo'],
		'tipos' => $tipos,
	));
	exit;
} elseif ($_POST['flag'] == "I") {
	$nome = mysqli_real_escape_string($conexao4, (string) $_POST['nome']);
	$chave = mysqli_real_escape_string($conexao4, (string) $_POST['chave']);
	$andaNeo = mysqli_real_escape_string($conexao4, (string) $_POST['anda_neo']);
	$especie = mysqli_real_escape_string($conexao4, (string) $_POST['especie']);
	$painel = mysqli_real_escape_string($conexao4, (string) $_POST['painel']);
	$titulo = mysqli_real_escape_string($conexao4, (string) $_POST['titulo']);
	$i  = " INSERT INTO andamentos SET";
	$i .= " nome   	 = '" . $nome . "', ";
	$i .= " chave 	 = '" . $chave . "', ";
	$i .= " anda_neo = '" . $andaNeo . "', ";
	$i .= " especie  = '" . $especie . "', ";
	$i .= " painel   = '" . $painel . "', ";
	$i .= " titulo   = '" . $titulo . "'  ";
	echo mysqli_query($conexao4, $i) ? 1 : mysqli_error($conexao4);
} elseif ($_POST['flag'] == "U") {
	$andaId = (int) $_POST['anda_id'];
	$nome = mysqli_real_escape_string($conexao4, (string) $_POST['nome']);
	$chave = mysqli_real_escape_string($conexao4, (string) $_POST['chave']);
	$andaNeo = mysqli_real_escape_string($conexao4, (string) $_POST['anda_neo']);
	$especie = mysqli_real_escape_string($conexao4, (string) $_POST['especie']);
	$painel = mysqli_real_escape_string($conexao4, (string) $_POST['painel']);
	$titulo = mysqli_real_escape_string($conexao4, (string) $_POST['titulo']);
	$i  = " UPDATE andamentos SET";
	$i .= " nome 		  = '" . $nome . "', ";
	$i .= " chave 		  = '" . $chave . "', ";
	$i .= " anda_neo 	  = '" . $andaNeo . "', ";
	$i .= " especie 	  = '" . $especie . "', ";
	$i .= " painel 	 	  = '" . $painel . "', ";
	$i .= " titulo 	  	  = '" . $titulo . "' ";
	$i .= " WHERE anda_id = " . $andaId;
	echo mysqli_query($conexao4, $i) ? 1 : mysqli_error($conexao4);
} elseif ($_POST['flag'] == "D") {
	mysqli_query($conexao4, "DELETE FROM `andamentos` WHERE `anda_id`='" . (int) $_POST['anda_id'] . "' LIMIT 1");
	echo 1;
}
?>
