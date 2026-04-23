<?php

include("seguranca.php");
protegePagina(0);

if($_POST['flag']=="E"){
	$semanas_id = $_POST['id_sem'];
	$return = "";
	$q  = " SELECT * FROM semanas";
	$q .= " WHERE semanas_id = $semanas_id";
	$query = mysqli_query($conexao4,$q);
	$while = mysqli_fetch_row($query);
	foreach($while as $w){
		echo $w . "-|-";
	}
}elseif($_POST['flag']=="I"){
	
	$q  = " SELECT * FROM semanas";
	$q .= " WHERE mes = '" . $_POST['mes_sem'] . "' and ano = '" . $_POST['ano_sem'] . "' " ;
	$qsel = mysqli_query($conexao4,$q);
	if(mysqli_fetch_row($qsel)==0){
		$i  = " INSERT INTO semanas SET";
		$i .= " mes = '" 	. $_POST['mes_sem'] 	. "', ";
		$i .= " ano = '" 	. str_replace('.','',str_replace(',','',$_POST['ano_sem'])) . "', ";
		$i .= " ini_1 = '" 	. $_POST['ini1_sem'] 	. "', ";
		$i .= " fim_1 = '" 	. $_POST['fim1_sem'] 	. "', ";
		$i .= " ini_2 = '" 	. $_POST['ini2_sem'] 	. "', ";
		$i .= " fim_2 = '" 	. $_POST['fim2_sem'] 	. "', ";
		$i .= " ini_3 = '" 	. $_POST['ini3_sem'] 	. "', ";
		$i .= " fim_3 = '" 	. $_POST['fim3_sem'] 	. "', ";
		$i .= " ini_4 = '" 	. $_POST['ini4_sem'] 	. "', ";
		$i .= " fim_4 = '" 	. $_POST['fim4_sem'] 	. "', ";
		$i .= " ini_5 = '" 	. ($_POST['ini5_sem']?$_POST['ini5_sem']:'0') . "', ";
		$i .= " fim_5 = '" 	. ($_POST['fim5_sem']?$_POST['fim5_sem']:'0') . "', ";
		$i .= " data_cad  = '" . date("Y-m-d H:i:s") ."', ";
		$i .= " data_arlt = '" . date("Y-m-d H:i:s") ."'  ";
		$query = mysqli_query($conexao4,$i);
		if($query){
			echo 1;
		}else{
			echo 3;
		}
	}else{
		echo 2;
	}
	
}elseif($_POST['flag']=="U"){
	$u  = " UPDATE semanas SET";
	$u .= " mes   = '" . $_POST['mes_sem']   . "', " ;
	$u .= " ano   = '" . $_POST['ano_sem']   . "', " ;
	$u .= " ini_1 = '" . $_POST['ini1_sem'] . "', " ;
	$u .= " fim_1 = '" . $_POST['fim1_sem'] . "', " ;
	$u .= " ini_2 = '" . $_POST['ini2_sem'] . "', " ;
	$u .= " fim_2 = '" . $_POST['fim2_sem'] . "', " ;
	$u .= " ini_3 = '" . $_POST['ini3_sem'] . "', " ;
	$u .= " fim_3 = '" . $_POST['fim3_sem'] . "', " ;
	$u .= " ini_4 = '" . $_POST['ini4_sem'] . "', " ;
	$u .= " fim_4 = '" . $_POST['fim4_sem'] . "', " ;
	$u .= " ini_5 = '" . $_POST['ini5_sem'] . "', " ;
	$u .= " fim_5 = '" . $_POST['fim5_sem'] . "', " ;
	$u .= " data_arlt  = '" . date("Y-m-d H:i:s") . "' " ;
	$u .= " WHERE semanas_id = " . $_POST['id_sem'] . " " ;
	$query = mysqli_query($conexao4,$u);
	echo 1;
}elseif($_POST['flag']=="D"){
	mysqli_query($conexao4,"DELETE FROM `semanas` WHERE `semanas_id`='" . $_POST['id_sem'] . "' LIMIT 1");
	echo 1;
}

?>