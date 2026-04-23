<?php

include("seguranca.php");
protegePagina(0);

if($_POST['flag']=="U")
{
	$i  = " UPDATE usuarios SET";
	if($_POST['senha_usu1']!="")
	{
		$i .= " senha_usu = '" . md5($_POST['senha_usu1']) . "' " ;
	}
	$i .= " WHERE id_usu = " . $_POST['id_usu'] . " " ;
	$query = mysqli_query($conexao4,$i);
	if($query){
		mysqli_query($conexao4,"UPDATE usuarios SET acesso_usu = '" . date("Y-m-d H:i:s") . "' where id_usu = " . $_POST['id_usu'] . " ");
	}
	echo 1;
}
else
{
	echo 2;
}

?>