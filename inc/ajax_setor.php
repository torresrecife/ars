<?php

include("seguranca.php");
protegePagina(0);

if($_POST['flag']=="E")
{
	$area_id = $_POST['area_id'];
	$return = "";
	$i = 0;
	$q  = " SELECT * FROM area";
	$q .= " WHERE area_id = $area_id";
	$query = mysqli_query($conexao4,$q);
	$while = mysqli_fetch_row($query);
	
	foreach($while as $w)
	{
		echo utf8_encode($w) . "-|-";
	}
}
elseif($_POST['flag']=="I")
{
	$i  = " INSERT INTO area SET";
	$i .= " area_nome = '" . $_POST['area_nome'] . "', " ;
	$i .= " area_date   = '"	. date("Y-m-d H:i:s")  . "' " ;
	$query = mysqli_query($conexao4,$i);
	echo 1;
}
elseif($_POST['flag']=="U")
{
	$i  = " UPDATE area SET";
	$i .= " area_nome 	   = '" . $_POST['area_nome'] . "' ";
	$i .= " WHERE area_id =  " . $_POST['area_id']   . " " ;
	$query = mysqli_query($conexao4,$i);
	echo 1;
}
elseif($_POST['flag']=="D")
{
	mysqli_query($conexao4,"DELETE FROM `area` WHERE `area_id`='" . $_POST['area_id'] . "' LIMIT 1");
	echo 1;
}
?>