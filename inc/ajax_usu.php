<?php

include("seguranca.php");
protegePagina(0);

if($_POST['flag']=="E"){
	$id_usu = $_POST['id_usu'];
	$return = "";
	$q  = " SELECT * FROM usuarios";
	$q .= " WHERE id_usu = $id_usu";
	$query = mysqli_query($conexao4,$q);
	$while = mysqli_fetch_row($query);
	foreach($while as $w){
		echo $w . "-|-";
	}
}elseif($_POST['flag']=="I"){
	$i  = " INSERT INTO usuarios SET";
	$i .= " nome_usu = '" 	. $_POST['nome_usu'] 	. "', " ;
	$i .= " login_usu = '" 	. $_POST['login_usu'] 	. "', " ;
	if($_POST['senha_usu1']!=""){
		$i .= " senha_usu = '" 	. md5($_POST['senha_usu1']) . "', " ;
	}
	$i .= " email_usu  = '"	. $_POST['email_usu']   . "', " ;
	$i .= " nivel_usu  = '"	. $_POST['nivel_usu']   . "', " ;
	$i .= " id_setor   = '"	. $_POST['setor_usu']   . "', " ;
	$i .= " id_cliente = '"	. $_POST['banco_neo'] 	. "', " ;
	$i .= " acesso_usu = '0000-00-00 00:00:00'," ;
	$i .= " data_cad   = '"	. date("Y-m-d H:i:s") . "' " ;
	$query = mysqli_query($conexao4,$i);
	echo 1;
}elseif($_POST['flag']=="U"){
	$u  = " UPDATE usuarios SET";
	$u .= " nome_usu = '"  . $_POST['nome_usu']  . "', " ;
	$u .= " login_usu = '" . $_POST['login_usu'] . "', " ;
	if($_POST['senha_usu1']!=""){
		$u .= " senha_usu = '" 	. md5($_POST['senha_usu1']) . "', " ;
	}
	$u .= " email_usu 	 = '" . $_POST['email_usu'] 	. "', " ;
	$u .= " nivel_usu 	 = '" . $_POST['nivel_usu'] 	. "', " ;
	$u .= " id_setor   	 = '" . $_POST['setor_usu'] 	. "', " ;
	$u .= " id_cliente	 = '" . $_POST['banco_neo'] 	. "' " ;
	$u .= " WHERE id_usu =  " . $_POST['id_usu'] 		. " " ;
	$query = mysqli_query($conexao4,$u);
	echo 1;
}elseif($_POST['flag']=="D"){
	mysqli_query($conexao4,"DELETE FROM `usuarios` WHERE `id_usu`='" . $_POST['id_usu'] . "' LIMIT 1");
	echo 1;
}
?>