<?php

include("seguranca.php");
protegePagina(0);
if($_POST['flag']=="E"){
	$banco_id  = $_POST['banco_id'];
	$return = "";
	$i  = 0;
	$q  = " SELECT * FROM bancos";
	$q .= " WHERE banco_id = $banco_id";
	$query = mysqli_query($conexao4,$q);
	$while = mysqli_fetch_row($query);
	header("Content-Type: text/html; charset=ISO-8859-1",true);
	foreach($while as $w){
		echo $w . "-|-";
	}
}elseif($_POST['flag']=="I"){
	$i  = " INSERT INTO bancos SET";
	$i .= " banco_name   = '" . $_POST['banco_name'] 	. "', ";
	$i .= " banco_cod 	 = '" . $_POST['banco_cod'] 	. "', ";
	$i .= " banco_creator= '" . date('Y-m-d H:i:s')		. "', ";
	$i .= " banco_area 	 = '" . $_POST['banco_area'] 	. "', ";
	$i .= " banco_status = '" . $_POST['banco_status'] 	. "', ";
	$i .= " banco_class  = '" . $_POST['banco_class'] 	. "', ";
	$i .= " simulador 	 = '" . $_POST['simulador'] 	. "', ";
	$i .= " banco_curto  = '" . $_POST['banco_curto'] 	. "'  ";
	$query = mysqli_query($conexao4,$i);
	
	$sel = mysqli_query($conexao4,"select max(b.banco_id) as 'nbanco' from bancos as b ");
	$whi = mysqli_fetch_array($sel);
	
	$c  = " INSERT INTO carteira SET"; 
	$c .= " banco_id 		 = '" . $whi['nbanco'] . "', ";
	$c .= " carteira_condicao = 'Carteira',"; 
	$c .= " carteira_cod 	 = '1',"; 
	$c .= " carteira_vinc 	 = 'IN',"; 
	$c .= " carteira_date 	 = '" . date('Y-m-d H:i:s') . "', ";
	$c .= " carteira_status  = 'Y' ";
	$query2 = mysqli_query($conexao4,$c);
	
	$car = mysqli_query($conexao4,"select max(c.carteira_id) as 'ncart' from carteira as c ");
	$wcr = mysqli_fetch_array($car);
	
	for($n=1;$n<=$_POST['cartei_num'];$n++){
		$d  = " INSERT INTO dados SET";
		$d .= " carteira_id='" . $wcr['ncart']  . "',";
		$d .= " banco_id='" . $whi['nbanco'] . "',";
		$d .= " dados_cod='" . $_POST['dados_name_'.$n]."',";
		$d .= " dados_date='" . date('Y-m-d H:i:s')."',";
		$d .= " dados_status='Y'";
		$query3 = mysqli_query($conexao4,$d);
	}
	echo 1;
}elseif($_POST['flag']=="U"){
	$u  = " UPDATE bancos SET";
	$u .= " banco_name 	 = '" . $_POST['banco_name']  	. "'," ;
	$u .= " banco_cod 	 = '" . $_POST['banco_cod'] 	. "'," ;
	$u .= " banco_creator= '" . date('Y-m-d H:i:s')		. "',";
	$u .= " banco_area 	 = '" . $_POST['banco_area'] 	. "'," ;
	$u .= " banco_status = '" . $_POST['banco_status'] 	. "'," ;
	$u .= " banco_class  = '" . $_POST['banco_class'] 	. "'," ;
	$u .= " simulador 	 = '" . $_POST['simulador'] 	. "'," ;
	$u .= " banco_curto  = '" . $_POST['banco_curto'] 	. "' " ;
	$u .= " WHERE banco_id = '" . $_POST['banco_id'] 	. "' ";
	$query = mysqli_query($conexao4,$u);
	
	mysqli_query($conexao4,"DELETE FROM `dados` WHERE `banco_id`='" . $_POST['banco_id'] . "' LIMIT 1");
	
	$car = mysqli_query($conexao4,"select c.carteira_id as 'ncart' from carteira as c where c.banco_id = '" . $_POST['banco_id'] . "'");
	$wcr = mysqli_fetch_array($car);
	
	for($n=1;$n<=$_POST['cartei_num'];$n++){
		$d  = " INSERT INTO dados SET";
		$d .= " carteira_id	='" . $wcr['ncart']  			. "', ";
		$d .= " banco_id	='" . $_POST['banco_id'] 		. "', ";
		$d .= " dados_cod	='" . $_POST['dados_name_'.$n]	. "', ";
		$d .= " dados_date	='" . date('Y-m-d H:i:s')		. "', ";
		$d .= " dados_status='Y'";
		$query3 = mysqli_query($conexao4,$d);
	}
	echo 1;
}elseif($_POST['flag']=="D"){
	mysqli_query($conexao4,"DELETE FROM `bancos` WHERE `banco_id`='" . $_POST['banco_id'] . "' LIMIT 1");
	mysqli_query($conexao4,"DELETE FROM `carteira` WHERE `banco_id`='" . $_POST['banco_id'] . "' LIMIT 1");
	mysqli_query($conexao4,"DELETE FROM `dados` WHERE `banco_id`='" . $_POST['banco_id'] . "' ");
	echo 1;
}
?>