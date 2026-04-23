<?php
function formata_data_extenso($strDate){
	$arrMonthsOfYear = array(1 => 'janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro');
	$intDayOfMonth = date("d");
	$intMonthOfYear = date("n");
	$intYear = date("Y");
	return $intDayOfMonth . ' de ' . $arrMonthsOfYear[$intMonthOfYear] . ' de ' . $intYear. '.';
}

function fc_select($p_tb,$p_id,$val_id,$val_nome,$usu,$conex,$p_setor=""){
	$q = mysqli_query($conex,"SELECT $val_id , $val_nome FROM " . $p_tb. " " . ($usu!="" ? "where tipo_usu = " . $usu : "") . " " . ($p_setor!="" ? "and id_setor = " . $p_setor : "") . " GROUP BY " . $val_nome . " ORDER BY " . $val_nome. " ");
	echo "<option></option>";
	
	while($w = mysqli_fetch_array($q)){
		echo "<option value='" . $w[$val_id] . "' " . ($w[$val_id] == "$p_id" ? "selected" : "") . ">" . $w[$val_nome] . "</option>";
	}
}
function fc_select_li($p_tb,$p_id,$val_id,$val_nome,$usu,$conex,$p_setor=""){
	$q = mysqli_query($conex,"SELECT $val_id , $val_nome FROM " . $p_tb. " " . ($usu!="" ? "where tipo_usu = " . $usu : "") . " " . ($p_setor!="" ? "and id_setor = " . $p_setor : "") . " GROUP BY " . $val_nome . " ORDER BY " . $val_nome. " ");	
	while($w = mysqli_fetch_array($q)){
		//echo "<li><a class='icon-16-copy' href='index.php?TIPOPET=".$w[$val_id]."' >" . $w[$val_nome] . "</a></li>";
		echo "<li><a class='icon-16-copy' href='#' onclick='EnviarDados(\"index.php\",\"$p_id\",".$w[$val_id].");' >" . $w[$val_nome] . "</a></li>";
	}
}
function fc_select_div($p_tb,$p_id,$val_id,$val_nome,$usu,$se,$conex,$p_setor=""){
	//$SETOR_1 = "";
	$q = mysqli_query($conex,"SELECT $val_id , $val_nome, nome_pre, nome_pos, id_setor FROM " . $p_tb. " " . ($usu!="" ? "where tipo_usu = " . $usu : "") . " " . ($p_setor!="" ? "and id_setor = " . $p_setor : "") . " GROUP BY " . $val_id . " ORDER BY nome_pre, " . $val_nome. " ");
	while($w = mysqli_fetch_array($q)){	
			$SETOR[$w['id_setor']] .= "<div class='icon-wrapper'>
							<div class='icon'>";
								if($se=="E"){
									$SETOR[$w['id_setor']] .= "<a href='#' onclick='mark_active(this)' class='clspet' grupo='0' numpet='" . $w[$val_id] . "'>";
								}elseif($se=="S"){
									$SETOR[$w['id_setor']] .= "<a href='#' onclick='EnviarDados(\"index.php\",\"$p_id\",".$w[$val_id].");'>";
								}
							$SETOR[$w['id_setor']] .= "<span style='float:left;position:absolute;font-size:7pt;padding:2px;color:#999'>"  . $w['nome_pre'] . "</span>";
							$SETOR[$w['id_setor']] .= "<img src='css/images/header/icon-48-article-edit.png' alt=''  />";
							$SETOR[$w['id_setor']] .= "<span style='position:relative;margin-top:-8px'> &nbsp; " . trim($w[$val_nome]) . " &nbsp; </span>
							</a>
						</div>
					</div>";
	
	}
	foreach($SETOR as $SET){
		echo $SET;
	}
}
function fc_select_dados($id_input,$conex,$p_setor=""){
	$q = mysqli_query($conex,"SELECT id_dados, nome_dados FROM tp_dados_tb where id_input = '$id_input' " . ($p_setor!="" ? "and id_setor = " . $p_setor : "") . " ORDER BY nome_dados asc ");
	echo "<option></option>";
	
	while($w = mysqli_fetch_array($q)){
		echo "<option value='" . $w['id_dados'] . "' " . ($w[$val_id] == "$p_id" ? "selected" : "") . ">" . $w['nome_dados'] . "</option>";
	}
}

function fc_select_name($cond,$where,$col,$banco,$conex){
	if($where!='' && $col !='' && $banco !=''){
		$campo = explode("|_|",$col);
		$sel  = " SELECT ";
		
		for($i=0;$i<=count($campo);$i++){
			if($campo[$i] != ''){
				$sel .= ($i> 0 ? (',' . $campo[$i]) : $campo[$i] );
			}
		}
		$sel .= " FROM $banco";
		$sel .= " where $cond = $where";
		$sel .= " limit 1";			
		$q = mysqli_query($conex,$sel);
		$w = mysqli_fetch_array($q);
		return $w[0];
		//return "SELECT $col FROM $banco where $cond = $where limit 1"; //$w[0];
	}else{
		return '';
	}
	
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
function fc_botoes_grp($id_list,$displ,$nome=""){
	return "<div id='module-status' style='display:" . $displ . ";'>
				<span class='editar'><a href='javascript:fc_edit_list(\"$id_list\",\"U\");' class='button_del' title='Editar Servidor'>Editar</a></span>
				<span class='excluir'><a href='javascript:fc_del_list(\"$id_list\",\"$nome\");' class='button_del' title='Excluir Servidor'>Excluir</a></span>
			</div>";
}
function fc_botoes_setor($id_setor,$displ,$nome=""){
	return "<div id='module-status' style='display:" . $displ . ";'>
				<span class='editar'><a href='javascript:fc_edit_setor(\"$id_setor\",\"U\");' class='button_del' title='Editar Setor'>Editar</a></span>
				<span class='excluir'><a href='javascript:fc_del_setor(\"$id_setor\",\"".utf8_encode($nome)."\");' class='button_del' title='Excluir Setor'>Excluir</a></span>
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