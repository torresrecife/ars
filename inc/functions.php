<?php
if (defined('ARS_LEGACY_FUNCTIONS_LOADED')) {
	return;
}

define('ARS_LEGACY_FUNCTIONS_LOADED', true);

function formata_data_extenso($strDate){
	$arrMonthsOfYear = array(1 => 'janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro');
	$intDayOfMonth = date("d");
	$intMonthOfYear = date("n");
	$intYear = date("Y");
	return $intDayOfMonth . ' de ' . $arrMonthsOfYear[$intMonthOfYear] . ' de ' . $intYear. '.';
}


//Maiúscula
function upwords($str){
	$valor = preg_replace('#\s(como?|d[aeo]s?|desde|para|por|que|sem|sob|sobre|trás)\s#ie', '" ".strtolower("\1")." "', ucwords($str));
	$valor = str_replace(" E "," e ",$valor);
	$valor = str_replace("S.a","S.A",$valor);
	$valor = str_replace(" O "," o ",$valor);
	$valor = str_replace(" No "," no ",$valor);
	$valor = str_replace(" N&ordm;"," n&ordm;",$valor);
	$valor = str_replace(" Nº"," nº",$valor);
	$valor = str_replace(" Cpf"," CPF",$valor);
	return $valor;
}

function convertemin($term){
    $palavra = strtr(strtolower($term),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß","àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ");
    return $palavra;
}

function limita_caracteres($texto, $limite, $quebra = true){
   $tamanho = strlen($texto);
   if($tamanho <= $limite){ //Verifica se o tamanho do texto é menor ou igual ao limite
      $novo_texto = $texto;
   }else{ // Se o tamanho do texto for maior que o limite
      if($quebra == true){ // Verifica a opção de quebrar o texto
         $novo_texto = trim(substr($texto, 0, $limite))."...";
      }else{ // Se não, corta $texto na última palavra antes do limite
         $ultimo_espaco = strrpos(substr($texto, 0, $limite), " "); // Localiza o útlimo espaço antes de $limite
         $novo_texto = trim(substr($texto, 0, $ultimo_espaco)).""; // Corta o $texto até a posição localizada
      }
   }
   return $novo_texto; // Retorna o valor formatado
}
function fc_botoes($valor,$displ){
	return "<div id='module-status' style='display:" . $displ . ";'>
				<span class='editar'><a href='javascript:fc_inputs(\"U\",\"" . $valor . "\");' class='button_del' title='Editar Campo'>Editar</a></span>
				<span class='excluir'><a href='javascript:fc_del_input(\"" . $valor . "\");' class='button_del' title='Excluir Campo'>Excluir</a></span>
			</div>";
}
function fc_botoes_usu($id_usu,$displ,$nome=""){
	return "<div id='module-status' style='display:" . $displ . ";'>
				<span class='editar'><a href='javascript:fc_edit_usu(\"$id_usu\",\"U\");' class='button_del' title='Editar Usuário'>Editar</a></span>
				<span class='excluir'><a href='javascript:fc_del_usu(\"$id_usu\",\"".utf8_encode($nome)."\");' class='button_del' title='Excluir Usuário'>Excluir</a></span>
			</div>";
}
function fc_botoes_sem($id_sem,$displ,$nome=""){
	return "<div id='module-status' style='display:" . $displ . ";'>
				<span class='editar'><a href='javascript:fc_edit_sem(\"$id_sem\",\"U\");' class='button_del' title='Editar a Semana'>Editar</a></span>
				<span class='excluir'><a href='javascript:fc_del_sem(\"$id_sem\",\"".utf8_encode($nome)."\");' class='button_del' title='Excluir Semana'>Excluir</a></span>
			</div>";
}
function fc_botoes_cliente($id_cliente,$displ,$nome=""){
	return "<div id='module-status' style='display:" . $displ . ";'>
				<span class='editar'><a href='javascript:fc_edit_cliente(\"$id_cliente\",\"U\");' class='button_del' title='Editar Cliente'>Editar</a></span>
				<span class='excluir'><a href='javascript:fc_del_cliente(\"$id_cliente\",\"".utf8_encode($nome)."\");' class='button_del' title='Excluir Cliente'>Excluir</a></span>
			</div>";
}
function fc_botoes_andamento($id_anda,$displ,$nome=""){
	return "<div id='module-status' style='display:" . $displ . ";'>
				<span class='editar'><a href='javascript:fc_edit_andamento(\"$id_anda\",\"U\");' class='button_del' title='Editar Andamento'>Editar</a></span>
				<span class='excluir'><a href='javascript:fc_del_andamento(\"$id_anda\",\"".utf8_encode($nome)."\");' class='button_del' title='Excluir Andamento'>Excluir</a></span>
			</div>";
}
function fc_botoes_metas($id_metas,$displ,$nome=""){
	return "<div id='module-status' style='display:" . $displ . ";'>
				<span class='editar'><a href='javascript:fc_edit_metas(\"$id_metas\",\"U\");' class='button_del' title='Editar Meta'>Editar</a></span>
				<span class='excluir'><a href='javascript:fc_del_metas(\"$id_metas\",\"".utf8_encode($nome)."\");' class='button_del' title='Excluir Meta'>Excluir</a></span>
			</div>";
}
function fc_botoes_setor($id_setor,$displ,$nome=""){
	return "<div id='module-status' style='display:" . $displ . ";'>
				<span class='editar'><a href='javascript:fc_edit_setor(\"$id_setor\",\"U\");' class='button_del' title='Editar Setor'>Editar</a></span>
				<span class='excluir'><a href='javascript:fc_del_setor(\"$id_setor\",\"".utf8_encode($nome)."\");' class='button_del' title='Excluir Setor'>Excluir</a></span>
			</div>";
}
function fc_botoes_regiao($id_regiao,$displ,$nome=""){
	return "<div id='module-status' style='display:" . $displ . ";'>
				<span class='editar'><a href='javascript:fc_edit_regiao(\"$id_regiao\",\"U\");' class='button_del' title='Editar Regiao'>Editar</a></span>
				<span class='excluir'><a href='javascript:fc_del_regiao(\"$id_regiao\",\"".utf8_encode($nome)."\");' class='button_del' title='Excluir Regiao'>Excluir</a></span>
			</div>";
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
$arrMonths = array(1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro');


?>
