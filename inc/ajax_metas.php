<?php

include("seguranca.php");
protegePagina(0);

//função para converter numeros 
function convert_moeda($valor){
	$valor1 = str_replace(".","",$valor);
	$valor2 = str_replace(",",".",$valor1);
	return $valor2;
}

if($_POST['flag']=="E"){
	$meta_id  = $_POST['meta_id'];
	$return = "";
	$i = 0;
	$q  = " SELECT * FROM metas_andamentos";
	$q .= " WHERE meta_id = $meta_id";
	$query = mysqli_query($conexao4,$q);
	$while = mysqli_fetch_row($query);
	header("Content-Type: text/html; charset=ISO-8859-1",true);
	foreach($while as $w){
		echo $w . "-|-";
	}
}elseif($_POST['flag']=="I"){
	$numes = (int) $_POST['numes'];
	for($s=1;$s<=$numes;$s++){		
		$i = "";
		$i .= " INSERT INTO metas_andamentos SET";
		$i .= " banco_id    = '" 	. $_POST['banco_id'] 		. "', ";
		$i .= " meta_mes 	= '" 	. $_POST['meta_mes'] 		. "', ";
		$i .= " meta_ano    = '" 	. $_POST['meta_ano'] 		. "', ";
		$i .= " anda_id 	= '" 	. $_POST['meta_name_'.$s] 	. "', ";
		$i .= " def_sem		= '"	. $_POST['def_sem_'.$s] 	. "', ";
		$i .= " sem_1  		= "	. ($_POST['sem1_valor_'.$s]!=""?"'".convert_moeda($_POST['sem1_valor_'.$s])."'":"null") . ", ";
		$i .= " sem_2  		= "	. ($_POST['sem2_valor_'.$s]!=""?"'".convert_moeda($_POST['sem2_valor_'.$s])."'":"null") . ", ";
		$i .= " sem_3  		= "	. ($_POST['sem3_valor_'.$s]!=""?"'".convert_moeda($_POST['sem3_valor_'.$s])."'":"null") . ", ";
		$i .= " sem_4  		= "	. ($_POST['sem4_valor_'.$s]!=""?"'".convert_moeda($_POST['sem4_valor_'.$s])."'":"null") . ", ";
		$i .= " sem_5  		= "	. ($_POST['sem5_valor_'.$s]!=""?"'".convert_moeda($_POST['sem5_valor_'.$s])."'":"null") . ", ";
		$i .= " meta_valor  = '". convert_moeda($_POST['meta_valor_'.$s]) . "'  ";
		$query = mysqli_query($conexao4,$i);
	}
	echo 1 ;
}elseif($_POST['flag']=="U"){
	
	$numes = (int) $_POST['numes'];
	for($s=1;$s<=$numes;$s++){		
		$i = "";
		$i .= " UPDATE metas_andamentos SET";
		$i .= " banco_id      = '" . $_POST['banco_id'] 	 . "'," ;
		$i .= " meta_mes 	  = '" . $_POST['meta_mes'] 	 . "'," ;
		$i .= " meta_ano      = '" . $_POST['meta_ano'] 	 . "'," ;
		$i .= " anda_id 	  = '" . $_POST['meta_name_'.$s] . "'," ;
		$i .= " def_sem		  = '" . $_POST['def_sem_'.$s] 	 . "', ";
		$i .= " sem_1  		  = "  . ($_POST['sem1_valor_'.$s]!=""?"'".convert_moeda($_POST['sem1_valor_'.$s])."'":"null") . ", ";
		$i .= " sem_2  		  = "  . ($_POST['sem2_valor_'.$s]!=""?"'".convert_moeda($_POST['sem2_valor_'.$s])."'":"null") . ", ";
		$i .= " sem_3  		  = "  . ($_POST['sem3_valor_'.$s]!=""?"'".convert_moeda($_POST['sem3_valor_'.$s])."'":"null") . ", ";
		$i .= " sem_4  		  = "  . ($_POST['sem4_valor_'.$s]!=""?"'".convert_moeda($_POST['sem4_valor_'.$s])."'":"null") . ", ";
		$i .= " sem_5  		  = "  . ($_POST['sem5_valor_'.$s]!=""?"'".convert_moeda($_POST['sem5_valor_'.$s])."'":"null") . ", ";
		$i .= " meta_valor	  = "  . ($_POST['meta_valor_'.$s]!=""?"'".convert_moeda($_POST['meta_valor_'.$s])."'":"null") . " ";
		$i .= " WHERE meta_id = '" . convert_moeda($_POST['meta_id']) . "' " ;
		$query = mysqli_query($conexao4,$i);
	}
	echo 1;
}elseif($_POST['flag']=="D"){
	mysqli_query($conexao4,"DELETE FROM `metas_andamentos` WHERE `meta_id`='" . $_POST['meta_id'] . "' LIMIT 1");
	echo 1;
}
?>