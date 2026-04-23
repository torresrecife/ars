<?php 


//carteiras jurídicas
$bancos2['ITAÚ'][] = " 'RCJ VAREJO - BANCO ITAUCARD S.A'";
$bancos2['ITAÚ'][] = " 'RCJ VAREJO - ITAU UNIBANCO S.A'";
$bancos2['ITAÚ'][] = " 'RECCREDITO - ITAU UNIBANCO S.A'";
$bancos2['SAFRA'][] = " 'RCJ VAREJO - SAFRA LEASING S/A ARRENDAMENTO MERCANTIL'";
$bancos2['SAFRA'][] = " 'RCJ VAREJO - BANCO J. SAFRA S/A'";
$bancos2['SAFRA'][] = " 'RCJ VAREJO - BANCO SAFRA S/A'";
$bancos2['VOLKSWAGEN'][] = " 'RCJ ATACADO - BANCO VOLKSWAGEN S.A'";
$bancos2['VOLKSWAGEN'][] = " 'RCJ VAREJO - BANCO VOLKSWAGEN S.A'";

$bancos[utf8_decode('ITAÚ')] = implode(",",$bancos2[utf8_decode('ITAÚ')]);
$bancos[utf8_decode('SAFRA')] = implode(",",$bancos2[utf8_decode('SAFRA')]);
$bancos[utf8_decode('VOLKSWAGEN')] = implode(",",$bancos2[utf8_decode('VOLKSWAGEN')]);

$mes = isset($_GET['mes'])?$_GET['mes']:(isset($_POST['ars_mes'])?$_POST['ars_mes']:date('m'));
$ano = $_GET['ano']?$_GET['ano']:($_POST['ars_ano']?$_POST['ars_ano']:date('Y'));

$day_ini = $_GET['ini']?$_GET['ini']:($_POST['ars_ini']?$_POST['ars_ini']:"");
$day_fim = $_GET['fim']?$_GET['fim']:($_POST['ars_fim']?$_POST['ars_fim']:"");

$ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));

$andamentos = array(
	utf8_decode('Distribuição')			=>utf8_decode("'Ação distribuída'"),
	utf8_decode('Liminar')				=>utf8_decode("'LIMINAR - DEFERIDA'"),
	utf8_decode('Mandado')				=>utf8_decode("'EXPEDIÇÃO DE MANDADO','Mandado Expedido','MANDADO EXPEDIDO - RCJ','MANDADO DESENTRANHADO'"),
	utf8_decode('Bem Localizado')		=>utf8_decode("'BEM LOCALIZADO'"),
	utf8_decode('Liberado para Venda')	=>utf8_decode("'LIBERADO PARA VENDA'"),
	utf8_decode('Alvarás')				=>utf8_decode("'ALVARÁ LEVANTADO'"),
	utf8_decode('Encerramento')			=>utf8_decode("'ENCERRAMENTO APROVADO - RCJ VAREJO'"),
	utf8_decode('Citação')				=>utf8_decode("'CITAÇÃO POSITIVA'"),
	utf8_decode('Retomadas')			=>utf8_decode("'Bem Retomado','Bem Apreendido','Entrega Amigável'"),
	utf8_decode('Carta Precatória')		=>utf8_decode("'Carta Precatória'"),
	utf8_decode('Falência')				=>utf8_decode("'HABILITACAO FALENCIA'"),
	utf8_decode('Faturamento')			=>utf8_decode("'HABILITACAO FALENCIA'")
);

//////////////////ITAÚ/////////////////////////////////////////////////////
$arr_meta[utf8_decode("ITAÚ")]  = ARRAY();
$arr_meta[utf8_decode("ITAÚ")][utf8_decode("Distribuição")] 			= 10;
$arr_meta[utf8_decode("ITAÚ")][utf8_decode("Citação")]					= 8;
$arr_meta[utf8_decode("ITAÚ")][utf8_decode("Penhora Positiva")]			= 0;
$arr_meta[utf8_decode("ITAÚ")][utf8_decode("Liminar")] 	 				= 0;
$arr_meta[utf8_decode("ITAÚ")][utf8_decode("Mandado")] 					= 0;
$arr_meta[utf8_decode("ITAÚ")][utf8_decode("Bem Localizado")] 			= 0;
$arr_meta[utf8_decode("ITAÚ")][utf8_decode("Alvarás")]					= 0;
$arr_meta[utf8_decode("ITAÚ")][utf8_decode("Liberado para Venda")] 		= 0;
$arr_meta[utf8_decode("ITAÚ")][utf8_decode("Encerramento")] 			= 4;
$arr_meta[utf8_decode("ITAÚ")][utf8_decode("Retomadas")] 				= 0;
$arr_meta[utf8_decode("ITAÚ")][utf8_decode("Carta Precatória")] 		= 2;
$arr_meta[utf8_decode("ITAÚ")][utf8_decode("Falência")]					= 1;
$arr_meta[utf8_decode("ITAÚ")][utf8_decode("Faturamento")]				= 8900.00;

//////////////////SAFRA/////////////////////////////////////////////////////
$arr_meta[utf8_decode("SAFRA")] = ARRAY();
$arr_meta[utf8_decode("SAFRA")][utf8_decode("Distribuição")] 		 	= 60;
$arr_meta[utf8_decode("SAFRA")][utf8_decode("Citação")]					= 0;
$arr_meta[utf8_decode("SAFRA")][utf8_decode("Penhora Positiva")]		= 0;
$arr_meta[utf8_decode("SAFRA")][utf8_decode("Liminar")] 	 			= 40;
$arr_meta[utf8_decode("SAFRA")][utf8_decode("Mandado")] 				= 50;
$arr_meta[utf8_decode("SAFRA")][utf8_decode("Bem Localizado")] 			= 50;
$arr_meta[utf8_decode("SAFRA")][utf8_decode("Alvarás")] 				= 6;
$arr_meta[utf8_decode("SAFRA")][utf8_decode("Liberado para Venda")] 	= 20;
$arr_meta[utf8_decode("SAFRA")][utf8_decode("Encerramento")] 			= 100;
$arr_meta[utf8_decode("SAFRA")][utf8_decode("Retomadas")] 				= 32;
$arr_meta[utf8_decode("SAFRA")][utf8_decode("Carta Precatória")]		= 0;
$arr_meta[utf8_decode("SAFRA")][utf8_decode("Falência")]				= 0;
$arr_meta[utf8_decode("SAFRA")][utf8_decode("Faturamento")]				= 43755.00;

//////////////////VOLKSWAGEN/////////////////////////////////////////////////////
$arr_meta[utf8_decode("VOLKSWAGEN")] = ARRAY();
$arr_meta[utf8_decode("VOLKSWAGEN")][utf8_decode("Distribuição")]	 	= 30;
$arr_meta[utf8_decode("VOLKSWAGEN")][utf8_decode("Citação")]			= 0;
$arr_meta[utf8_decode("VOLKSWAGEN")][utf8_decode("Penhora Positiva")]	= 0;
$arr_meta[utf8_decode("VOLKSWAGEN")][utf8_decode("Liminar")]			= 30;
$arr_meta[utf8_decode("VOLKSWAGEN")][utf8_decode("Mandado")]			= 50;
$arr_meta[utf8_decode("VOLKSWAGEN")][utf8_decode("Bem Localizado")]		= 30;
$arr_meta[utf8_decode("VOLKSWAGEN")][utf8_decode("Alvarás")]			= 8;
$arr_meta[utf8_decode("VOLKSWAGEN")][utf8_decode("Liberado para Venda")]= 20;
$arr_meta[utf8_decode("VOLKSWAGEN")][utf8_decode("Encerramento")]	 	= 300;
$arr_meta[utf8_decode("VOLKSWAGEN")][utf8_decode("Retomadas")]	 		= 30;
$arr_meta[utf8_decode("VOLKSWAGEN")][utf8_decode("Carta Precatória")]	= 0;
$arr_meta[utf8_decode("VOLKSWAGEN")][utf8_decode("Falência")]			= 0;
$arr_meta[utf8_decode("VOLKSWAGEN")][utf8_decode("Faturamento")]		= 45950.00;

$dados[] = "'" . utf8_decode('RCJ ATACADO - BANCO ') . "',''";
$dados[] = "'" . utf8_decode('RCJ VAREJO - BANCO J. ') . "',''";
$dados[] = "'" . utf8_decode('RCJ VAREJO - BANCO ') . "',''";
$dados[] = "'" . utf8_decode('RCJ VAREJO - ') . "',''";
$dados[] = "'" . utf8_decode('RECCREDITO - ') . "',''";
$dados[] = "'" . utf8_decode(' UNIBANCO S.A') . "',''";
$dados[] = "'" . utf8_decode(' S/A') . "',''";
$dados[] = "'" . utf8_decode(' S.A') . "',''";
$dados[] = "'" . utf8_decode(' S/A - PROJETO REDE') . "',''";
$dados[] = "'" . utf8_decode('CARD S.A') . "',''";
$dados[] = "'" . utf8_decode(' LEASING S/A ARRENDAMENTO MERCANTIL') . "',''";
$dados[] = "'" . utf8_decode(' LEASING ARRENDAMENTO MERCANTIL') . "',''";
$dados[] = "'" . utf8_decode('ITAU')."','".utf8_decode('ITAÚ')."'";

//print_r($dados);

?>