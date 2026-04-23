<?php

include("seguranca.php");
protegePagina(0);
if($_POST['flag']=="E"){
	$anda_id  = $_POST['anda_id'];
	$return = "";
	$i = 0;
	$q  = " SELECT * FROM andamentos";
	$q .= " WHERE anda_id = $anda_id";
	$query = mysqli_query($conexao4,$q);
	$while = mysqli_fetch_row($query);
	header("Content-Type: text/html; charset=ISO-8859-1",true);
	foreach($while as $w){
		echo $w . "-|-";
	}
}elseif($_POST['flag']=="I"){
	$i  = " INSERT INTO andamentos SET";
	$i .= " nome   	 = '" . $_POST['nome'] 	  . "', ";
	$i .= " chave 	 = '" . $_POST['chave']	  . "', ";
	$i .= " anda_neo = '" . $_POST['anda_neo']. "', ";
	$i .= " especie  = '" . $_POST['especie'] . "', ";
	$i .= " painel   = '" . $_POST['painel']  . "', ";
	$i .= " titulo   = '" . $_POST['titulo']  . "'  ";
	$query = mysqli_query($conexao4,$i);
	echo 1;
}elseif($_POST['flag']=="U"){
	$i  = " UPDATE andamentos SET";
	$i .= " nome 		  = '" . $_POST['nome']  	. "', " ;
	$i .= " chave 		  = '" . $_POST['chave'] 	. "', " ;
	$i .= " anda_neo 	  = '" . $_POST['anda_neo'] . "', " ;
	$i .= " especie 	  = '" . $_POST['especie']  . "', " ;
	$i .= " painel 	 	  = '" . $_POST['painel']   . "', " ;
	$i .= " titulo 	  	  = '" . $_POST['titulo']   . "'  " ;
	$i .= " WHERE anda_id = "  . $_POST['anda_id'] 	. "   " ;
	$query = mysqli_query($conexao4,$i);
	echo 1;
}elseif($_POST['flag']=="D"){
	mysqli_query($conexao4,"DELETE FROM `andamentos` WHERE `anda_id`='" . $_POST['anda_id'] . "' LIMIT 1");
	echo 1;
}
?>