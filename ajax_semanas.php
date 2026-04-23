<?php

function diasemana($data) {
	$ano =  substr("$data", 0, 4);
	$mes =  substr("$data", 5, -3);
	$dia =  substr("$data", 8, 9);

	return $diasemana = date("w", mktime(0,0,0,$mes,$dia,$ano));
	
}

function Vsemana($valor){
	switch(diasemana($valor)){
		case 0:
			return 6;
			break;
		case 1:
			return 5;
			break;
		case 2:
			return 4;
			break;
		case 3:
			return 3;
			break;
		case 4:
			return 2;
			break;
		case 5:
			return 8;
			break;
		case 6:
			return 7;
			break;
	}
}

function ultimodia($mes,$ano){
	return $ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano)); // Mágica, plim!
}
function P_semana($mes,$ano,$num,$par){
	
	$fim_0 = $ano."-".$mes."-01";
	$fim_1 = date('Y-m-d', strtotime("+".Vsemana($fim_0)." days",strtotime($fim_0))); 
	$fim_2 = date('Y-m-d', strtotime("+".Vsemana($fim_1)." days",strtotime($fim_1))); 
	$fim_3 = date('Y-m-d', strtotime("+".Vsemana($fim_2)." days",strtotime($fim_2))); 
	$fim_4 = date('Y-m-d', strtotime("+".Vsemana($fim_3)." days",strtotime($fim_3))); 
	$fim_5 = date('Y-m-d', strtotime("+".Vsemana($fim_4)." days",strtotime($fim_4))); 
	
	$ini_1 = date('Y-m-d', strtotime("+0 days",strtotime($fim_0)));
	$ini_2 = date('Y-m-d', strtotime("+".(Vsemana($fim_0)+1)." days",strtotime($fim_0)));
	$ini_3 = date('Y-m-d', strtotime("+".(Vsemana($fim_1)+1)." days",strtotime($fim_1)));
	$ini_4 = date('Y-m-d', strtotime("+".(Vsemana($fim_2)+1)." days",strtotime($fim_2)));
	$ini_5 = date('Y-m-d', strtotime("+".(Vsemana($fim_3)+1)." days",strtotime($fim_3)));
	
	for($z=1;$z<=5;$z++){
		if($num==$z && $par=="ini"){
			return ${"ini_".$z};
		}elseif($num==$z && $par=="fim"){
			if($z==5){
				return $ano . "-". $mes . "-". ultimodia($mes,$ano);
			}else{
				return ${"fim_".$z};
			}
		}
	}
	
}

if($_POST['mes']){
	$mes = $_POST['mes'];
	$ano = $_POST['ano'];
	$sem = $_POST['sem'];
	$sem = $_POST['sem'];
	$par = $_POST['par'];
	
	echo date('d', strtotime(P_semana($mes,$ano,$sem,$par)));
	
}else{
	echo "Preencha a data corretamente!";
}


?>