<?php
if (defined('ARS_LEGACY_FUNCTIONS_LOADED')) {
	return;
}

define('ARS_LEGACY_FUNCTIONS_LOADED', true);

function formata_data_extenso($strDate){
	$arrMonthsOfYear = array(1 => 'janeiro','fevereiro','marÃ§o','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro');
	$intDayOfMonth = date("d");
	$intMonthOfYear = date("n");
	$intYear = date("Y");
	return $intDayOfMonth . ' de ' . $arrMonthsOfYear[$intMonthOfYear] . ' de ' . $intYear. '.';
}


//MaiÃºscula
function upwords($str){
	$valor = preg_replace('#\s(como?|d[aeo]s?|desde|para|por|que|sem|sob|sobre|trÃ¡s)\s#ie', '" ".strtolower("\1")." "', ucwords($str));
	$valor = str_replace(" E "," e ",$valor);
	$valor = str_replace("S.a","S.A",$valor);
	$valor = str_replace(" O "," o ",$valor);
	$valor = str_replace(" No "," no ",$valor);
	$valor = str_replace(" N&ordm;"," n&ordm;",$valor);
	$valor = str_replace(" NÂº"," nÂº",$valor);
	$valor = str_replace(" Cpf"," CPF",$valor);
	return $valor;
}

function convertemin($term){
    $palavra = strtr(strtolower($term),"Ã€ÃÃ‚ÃƒÃ„Ã…Ã†Ã‡ÃˆÃ‰ÃŠÃ‹ÃŒÃÃŽÃÃÃ‘Ã’Ã“Ã”Ã•Ã–Ã—Ã˜Ã™ÃœÃšÃžÃŸ","Ã Ã¡Ã¢Ã£Ã¤Ã¥Ã¦Ã§Ã¨Ã©ÃªÃ«Ã¬Ã­Ã®Ã¯Ã°Ã±Ã²Ã³Ã´ÃµÃ¶Ã·Ã¸Ã¹Ã¼ÃºÃ¾Ã¿");
    return $palavra;
}

function limita_caracteres($texto, $limite, $quebra = true){
   $tamanho = strlen($texto);
   if($tamanho <= $limite){ //Verifica se o tamanho do texto Ã© menor ou igual ao limite
      $novo_texto = $texto;
   }else{ // Se o tamanho do texto for maior que o limite
      if($quebra == true){ // Verifica a opÃ§Ã£o de quebrar o texto
         $novo_texto = trim(substr($texto, 0, $limite))."...";
      }else{ // Se nÃ£o, corta $texto na Ãºltima palavra antes do limite
         $ultimo_espaco = strrpos(substr($texto, 0, $limite), " "); // Localiza o Ãºtlimo espaÃ§o antes de $limite
         $novo_texto = trim(substr($texto, 0, $ultimo_espaco)).""; // Corta o $texto atÃ© a posiÃ§Ã£o localizada
      }
   }
   return $novo_texto; // Retorna o valor formatado
}

function diasemana($data) {
	$ano =  substr("$data",0,4);
	$mes =  substr("$data",5,-3);
	$dia =  substr("$data",8,9);

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

function diff_data($data1,$data2){
	$date1=date_create($data1);
	$date2=date_create($data2);
	$diff=date_diff($date1,$date2);
	return $diff->format("%a");
}
function ultimodia($mes,$ano){
	return $ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano)); // MÃ¡gica, plim!
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
$arrMonths = array(1=>'Janeiro',2=>'Fevereiro',3=>'MarÃ§o',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro');


?>
