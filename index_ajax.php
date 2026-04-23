<?php 
//ini_set('display_errors',1);
//ini_set('display_startup_erros',1);
//error_reporting(E_ALL);

if($_POST['hid_flag']==""){
	echo "<br><br><br><br><br>";
	echo "<div style='font-size:18px;height:50px;text-align:center'>Volte e selecione o Banco!</div>";
	echo "<div style='font-size:18px;height:50px;text-align:center'><input type='button' onclick='javascript:window.history.back()' value='Voltar' style='cursor:pointer;height:30px;width:100px'/></div>";
	exit;
}

$banco = $_POST['hid_flag'];
//$startDate = isset($_POST['startDate'])?$_POST['startDate']:date('M');

$mes = isset($_POST['mes'])?$_POST['mes']:date('m');
$ano = isset($_POST['ano'])?$_POST['ano']:date('Y');
$startDate = $arrMonths[(int) $mes] . " / ". $ano;

$Sb = "select b.banco_id,b.banco_cod,b.banco_name from bancos as b where b.banco_id='" . $banco . "' ";
$Qb = mysqli_query($conexao4,$Sb);
$Wb = mysqli_fetch_array($Qb);

$Ssem = "select * from semanas as s where s.mes='" . $mes . "' and s.ano='" . $ano . "' limit 1 ";
$Qsem = mysqli_query($conexao4,$Ssem);
$Wsem = mysqli_fetch_array($Qsem);

echo "<br><div style='font-family:arial;margin-left:40px;font-size:10pt;'>Cliente: <b>" . $Wb['banco_cod'] . "</b> | Mês / Ano: <b>$startDate</b> <a href='#' onclick='send_nav(\"" . 1 . "\",\"" . $Wb['banco_id'] . "\",\"p\")' >◄</a> <a href='#' onclick='send_nav(\"" . 1 . "\",\"" . $Wb['banco_id'] . "\",\"n\")'>►</a></div><br>";
echo "<input type='hidden' name='mes' id='mes' class='date-picker' value='$mes'/>";
echo "<input type='hidden' name='ano' id='ano' class='date-picker' value='$ano'/>";	
								
///define as semanas///
if($Wsem['ini_5']==0 || $Wsem['fim_5']==0){
	$nsem=4; 
	$ncol=14;
}else{
	$nsem=5; 
	$ncol=17;
}

$num=1;
$col=0;
$backg = "background:#F0F0F0";

$html_0 = "";
$html_1 = "";
$html_2 = "";
$html_3 = "";
$table = "";

$html_0 = "<meta http-equiv='content-type' content='text/html; charset=utf-8'>";
$html_0 = "<script>
	function send_form(valor1,valor2,valor3){
		if(valor3=='and'){
			$('#codig_and').val(valor1);
			$('#banco_and').val(valor2);
			$('#form_ars').attr('action','dados_anda.php');
		}else if(valor3=='fat'){
			$('#codig_lnc').val(valor1);
			$('#banco_lnc').val(valor2);
			$('#form_ars').attr('action','dados_fatur.php');
		}
		$('#form_ars').attr('target','_blank');
		$('#form_ars').submit();
	}
	function send_nav(valor1,valor2,valor3){
		var m_mes = $('#mes').val();
		add_month(m_mes,valor3);
		EnviarDados('index.php',2,valor1,valor2);
	}
	function add_month(meses,valor){
		var n_mes = 0;
		var n_ano = parseFloat($('#ano').val());
		if(meses==12 && valor=='n'){
			n_mes=1;
			$('#ano').val(n_ano+1);
		}else if(meses==01 && valor=='p'){
			n_mes=12;
			$('#ano').val(n_ano-1);
		}else{
			if(valor=='n'){
				n_mes = parseFloat(meses) + 1;
			}else if(valor=='p'){
				n_mes = parseFloat(meses) - 1;
			}
		}
		$('#mes').val(n_mes);
		//alert(n_mes);
	}
</script>

<style>
td{
	border-left-width: 1px;
	border:1px dotted #999;
}
.cls_dados{
	background:#DBDBDB;
	height:18px;
}
.cls_sema{
	background: #ccc;
	font-weight:bold;
	height:18px;
}
.cls_sema2{
	background: #ccc;
	font-weight:bold;
}
.cls_colun{
	width:2%;
	background: #1C86EE;
}
.box{
	margin-left:5px;
	float:left;
	/*width:10px;*/
	/*height:10px;*/
	/*border:1px solid #ccc;*/
	/*border-radius: 10px;*/
}
.cls_colun_2{
	width:2%;
	background: #FFB90F;
}
.cls_vals{
	width:" . ($ncol==17?" 4%":" 5%") . ";
}
.cls_vals2{
	height:25px;
	width:" . ($ncol==17?" 4%":" 5%") . ";
	border-top: 1px dotted #999;
	border-bottom: 1px dotted #999;
}
.cls_indic{
	width:13%;
	padding-left: 5px;
}
.cls_real:hover{
	background:#ebebeb;
	cursor:pointer;
}
.cls_bk{
	background:#F5F6CE;
}
.cls_bk2{
	background:#F2F3C5;
}
</style>";

$table .= $html_0;

$n=0;
//////////////////////////////////
$sel  = " SELECT * ";
$sel .= " FROM andamentos AS a ";
//$sel .= " JOIN banco_andamentos AS b ON a.anda_id=b.anda_id ";
$sel .= " JOIN metas_andamentos AS b ON a.anda_id=b.anda_id ";
$sel .= " WHERE b.banco_id=" . $Wb['banco_id'] . " ";
$sel .= " AND a.especie=1 ";
//$sel .= " AND b.stt='Y' ";
$sel .= " AND b.meta_mes=$mes ";
$sel .= " AND b.meta_ano=$ano ";
$sel .= " GROUP BY b.banco_id,b.anda_id ";
$sel .= " ORDER BY a.especie asc, a.ordem asc ";
$Qsel = mysqli_query($conexao4,$sel);
$Wrow = mysqli_num_rows($Qsel);

//$hei_p = $Wrow*($Wrow>4?7:8);
	$html_1 .= "<table align='center' height='auto' width='100%' border='0' cellspacing='1' cellpadding='1' id='tb_pro' style='font-family:Tahoma;font-size:8pt; border-collapse: collapse;'>";
	$html_1 .= "<tr>";
	$html_1 .= "<td align='center' rowspan='2' style='border:0' style='width:5px'></td>";
	$html_1 .= "<td align='center' rowspan='2' class='cls_sema cls_indic'>INDICADOR</td>";
			if($Wsem['ini_1'] != "" && $Wsem['fim_1'] !=""){
				$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" .  $Wsem['ini_1'] . " à " . $Wsem['fim_1'] . "</td>";
				$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" .  $Wsem['ini_2'] . " à " . $Wsem['fim_2'] . "</td>";
				$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" .  $Wsem['ini_3'] . " à " . $Wsem['fim_3'] . "</td>";
				$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" .  $Wsem['ini_4'] . " à " . $Wsem['fim_4'] . "</td>";
				if($ncol==17){
					$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . $Wsem['ini_5'] . " à " . $Wsem['fim_5'] . "</td>";
				}
				$html_1 .= "<td align='center' colspan='3' rowspan='1' class='cls_sema cls_vals'>TOTAL		</td>";
				$dia_s[] = (date('d', strtotime(P_semana($mes,$ano,1,"fim"))) - date('d', strtotime(P_semana($mes,$ano,1,"ini"))));
				$dia_s[] = (date('d', strtotime(P_semana($mes,$ano,2,"fim"))) - date('d', strtotime(P_semana($mes,$ano,2,"ini"))));
				$dia_s[] = (date('d', strtotime(P_semana($mes,$ano,3,"fim"))) - date('d', strtotime(P_semana($mes,$ano,3,"ini"))));
				$dia_s[] = (date('d', strtotime(P_semana($mes,$ano,4,"fim"))) - date('d', strtotime(P_semana($mes,$ano,4,"ini"))));
				$dia_s[] = (date('d', strtotime(P_semana($mes,$ano,5,"fim"))) - date('d', strtotime(P_semana($mes,$ano,5,"ini"))));
				$diasM=0;
				foreach($dia_s as $d){
					$dm = (($d==8?(3):($d==7?(2):($d==6?(1):0))));
					$diasM += ($d - $dm);
				}
				
			}else{
				$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . date('d', strtotime(P_semana($mes,$ano,1,"ini"))) . " a " . date('d', strtotime(P_semana($mes,$ano,1,"fim"))) . "</td>";
				$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . date('d', strtotime(P_semana($mes,$ano,2,"ini"))) . " a " . date('d', strtotime(P_semana($mes,$ano,2,"fim"))) . "</td>";
				$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . date('d', strtotime(P_semana($mes,$ano,3,"ini"))) . " a " . date('d', strtotime(P_semana($mes,$ano,3,"fim"))) . "</td>";
				$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . date('d', strtotime(P_semana($mes,$ano,4,"ini"))) . " a " . date('d', strtotime(P_semana($mes,$ano,4,"fim"))) . "</td>";
				if($ncol==17){
					$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . date('d', strtotime(P_semana($mes,$ano,5,"ini"))) . " a " . date('d', strtotime(P_semana($mes,$ano,5,"fim"))) . "</td>";
				}
				$html_1 .= "<td align='center' colspan='3' rowspan='1' class='cls_sema2 cls_vals'>TOTAL		</td>"; 
			}
		$html_1 .= "</tr>";
		$html_1 .= "<tr>";
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >META			</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >REALIZADO	</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >FAROL		</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >META			</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >REALIZADO	</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >FAROL		</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >META			</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >REALIZADO	</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >FAROL		</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >META			</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >REALIZADO	</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >FAROL		</td>";
			if($ncol==17){
				$html_1 .= "<td align='center' class='cls_dados cls_vals'>META		</td>";
				$html_1 .= "<td align='center' class='cls_dados cls_vals'>REALIZADO	</td>";
				$html_1 .= "<td align='center' class='cls_dados cls_vals'>FAROL		</td>";
				$html_1 .= "<td align='center' class='cls_dados cls_bk2' >META		</td>";
				$html_1 .= "<td align='center' class='cls_dados cls_bk2' >REALIZADO	</td>";
				$html_1 .= "<td align='center' class='cls_dados cls_bk2' >FAROL		</td>"; 
			}else{
				$html_1 .= "<td align='center' class='cls_dados cls_bk2' >META		</td>";
				$html_1 .= "<td align='center' class='cls_dados cls_bk2' >REALIZADO	</td>";
				$html_1 .= "<td align='center' class='cls_dados cls_bk2' >FAROL		</td>";
			}
		$html_1 .= "</tr>";
if($Wrow>0){
	while($Wsel = mysqli_fetch_array($Qsel)){
		$n++;
		$tt_meta=0;
		$meta_sum=0;
		$lancamentos = str_replace(",","','",$Wsel['anda_neo']);
		//$Qmeta = mysqli_query($conexao4,"SELECT * FROM metas_producao AS m WHERE m.banco_id=".$Wsel['banco_id']." AND m.mes=" . $mes . " AND m.ano=" . $ano . " ");
		//$Wmeta = mysqli_fetch_array($Qmeta);
		
		$Qmeta = mysqli_query($conexao4,"SELECT * FROM metas_andamentos AS m WHERE m.banco_id=".$Wsel['banco_id']." AND m.meta_mes=" . $mes . " AND m.meta_ano=" . $ano . " AND m.anda_id = ".$Wsel['anda_id']." ");
		$Wmeta = mysqli_fetch_array($Qmeta);
		
		$Qct = mysqli_query($conexao4,"SELECT c.carteira_vinc FROM carteira AS c WHERE c.banco_id=".$Wsel['banco_id']." AND c.carteira_condicao='Carteira' ");
		$Wct = mysqli_fetch_array($Qct);
		$carteira_vinc = $Wct['carteira_vinc'];
		
		$condicao="";
		$Qcart = mysqli_query($conexao4,"SELECT d.dados_cod,c.carteira_vinc,c.carteira_cod FROM dados AS d JOIN carteira AS c ON c.banco_id=d.banco_id WHERE d.banco_id=".$Wsel['banco_id']." ");
		$qtds = mysqli_num_rows($Qcart);
		$s=0;
		while($Wcart = mysqli_fetch_array($Qcart)){
			$s++;
			$condicao .=  "'" . ($Wcart['dados_cod']) . "'";
			if($qtds>$num++){
				$condicao .=  ",";
			}	
		}
		$num=1;
		$c=0;
		$tt=0;
		$codig_and = array();
		$codig_now = array();
		$html_1 .= "<tr style='height:30px'>";
		if($n==1){
			$html_1 .= "<td align='center' rowspan='".$Wrow."' class='cls_colun'><div style='color:#FFF;transform: rotate(270deg);width:20px'><b>OPERAÇÃO</b></div></td>";
		}
		for($m=1;$m<=$ncol;$m++){
			if($m==1){
				$html_1 .= "<td class='cls_indic'>".$Wsel['nome']."</td>";
			}else{
				if( (($m==2 || $m==5 || $m==8 || $m==11) && $ncol==14) ||
					(($m==2 || $m==5 || $m==8 || $m==11 || $m==14) && $ncol==17) ){
					
					$col++;
					if($Wsel['def_sem']=="N"){
						//calcula a meta da semana de forma automática
						$dD = ($Wsem['fim_'.$col]-$Wsem['ini_'.$col]);
						$dmais = (($dD==8?(2):($dD==7?(2):($dD==6?(1):($dD==5?(0):($dD==4?(-1):($dD==9?(1):"")))))));
						$diasD = ($dD - $dmais);
						$meta = number_format($diasD * (number_format($Wmeta['meta_valor'],0,"","")/$diasM),0,'','');
						////calcula a meta para acrescentar no final///////
						$meta_sum += $meta;
						if( ($m==11 && $ncol==14) || ($m==14 && $ncol==17) ){
							$tt_meta = $meta_sum;
							$tt_meta = (number_format($Wmeta['meta_valor'],0,"","")-$meta_sum)+$meta;
						}else{
							$tt_meta = $meta;
						}
					}elseif($Wsel['def_sem']=="Y"){
						//meta da semana de forma manual(fixa)
						$tt_meta=number_format($Wsel['sem_'.$col],0,"","");
					}
					$html_1 .= "<td align='center' class='cls_vals' style='$backg'>" . $tt_meta . "</td>";
					
				}else{
					
					if( (($m==3 || $m==6 || $m==9 || $m==12) && $ncol==14) ||
						(($m==3 || $m==6 || $m==9 || $m==12 || $m==15)  && $ncol==17) ){
						/////////pegando os valores depositados
						$c++;
						$qntd0  = " SELECT 1 as 'qtd' FROM v_Andamento_Processo AS a WITH (NOLOCK) ";
						$qntd1  = " SELECT a.CodigoAndamento FROM v_Andamento_Processo AS a WITH (NOLOCK) ";
						$qntd  = " JOIN v_Processo AS p WITH (NOLOCK) ON p.CodigoProcesso=a.CodigoProcesso ";
						$qntd .= " WHERE a.TipoAndamentoProcesso IN ('".$lancamentos."') "; 
						$qntd .= " AND p.TipoProcesso NOT IN ('CARTA PRECATÓRIA') ";
						if($carteira_vinc=="LIKE"){
							$qntd .= " AND p.Carteira LIKE '%".str_replace("'","",$condicao)."%' ";
						}else{
							$qntd .= " AND p.Carteira IN (".$condicao.") ";
						}
						if( $Wsem["ini_".$c] != "" && $Wsem["fim_".$c]!=""){
							$qntd .= " AND (day(a.DataHoraEvento)>=" . $Wsem["ini_".$c] . " AND day(a.DataHoraEvento)<=" . $Wsem["fim_".$c] . ")";
						}else{
							$qntd .= " AND (day(a.DataHoraEvento)>=" . date('d', strtotime(P_semana($mes,$ano,$c,"ini"))) . " AND day(a.DataHoraEvento)<=" . date('d', strtotime(P_semana($mes,$ano,$c,"fim"))) . ")";
						}
						$qntd .= " AND MONTH(a.DataHoraEvento)=$mes ";
						$qntd .= " AND YEAR(a.DataHoraEvento)=$ano ";
						$qntd .= " AND p.TipoDesdobramento IS NULL AND a.Invalido='False' ";
						$qntd .= " GROUP BY a.CodigoAndamento ";
						///coletar os codigos dos andamentos
						$Qcod = sqlsrv_query($conexao1,($qntd1.$qntd));
						while($Wcod = sqlsrv_fetch_array($Qcod, SQLSRV_FETCH_ASSOC)){
							$codig_and[] = $Wcod['CodigoAndamento'];
							$codig_now[] = $Wcod['CodigoAndamento'];
						}
						$Qqntd = sqlsrv_query($conexao1,($qntd0.$qntd));
						$banq=0;
						//$Wbanc = sqlsrv_fetch_array($Qqntd, SQLSRV_FETCH_ASSOC);
						while($Wbanc = sqlsrv_fetch_array($Qqntd, SQLSRV_FETCH_ASSOC)){
							$banq += $Wbanc['qtd'];
						}
						$html_1 .= "<td align='center' class='cls_vals cls_real' onclick='send_form(\"" . implode(',',$codig_now) ."\",\"" . $Wb['banco_name'] . "\",\"and\");'>".$banq."</td>";
						$codig_now=array();
						$tt += $banq;
						
					}else{
						$resul = number_format(($banq/$tt_meta )*100,0,',','');
						
						if($resul>=100 && $resul<110){
							$bol = "circle_green.png";
						}elseif($resul<100 && $resul>=80){
							$bol = "circle_yellow.png";
						}elseif($resul>=110){
							$bol = "circle_blue.png";
						}elseif($tt_meta==0){
							$bol = "circle_grey.png";
						}else{
							$bol = "circle_red.png";
						}
						if($m==$ncol){
							$tot = number_format(($tt/number_format($Wmeta['meta_valor'],0,"",""))*100,0,',','');
							if($tot>=100 && $tot<110){
								$cor = "circle_green.png";
							}elseif($tot<100 && $tot>=80){
								$cor = "circle_yellow.png";
							}elseif($tot>=110){
								$cor = "circle_blue.png";
							}elseif($Wmeta['meta_valor']==0){							
								$cor = "circle_grey.png";								
							}else{
								$cor = "circle_red.png";
							}
							$html_1 .= "<td align='center' class='cls_vals cls_real cls_bk' style='background:#F2F5A9'><b>". number_format($Wmeta['meta_valor'],0,"","") . "</b></td>";
							$html_1 .= "<td align='center' class='cls_vals cls_real cls_bk' onclick='send_form(\"" . implode(',',$codig_and) ."\",\"" . $Wb['banco_name'] . "\",\"and\");'><b>".$tt . "</b></td>";
							$html_1 .= "<td align='center' class='cls_vals cls_bk' ><img src='http://10.81.11.202/img/$cor' class='box' />".$tot."%</td>";
							$codig_and=array();
						}else{
							$html_1 .= "<td align='center' class='cls_vals'><img src='http://10.81.11.202/img/$bol' class='box' />" . ($tt_meta!=0?number_format(($banq/($tt_meta))*100,0,',',''):0)."%</td>";
						}
					}
				}
			}
		}
		$col=0;
		$html_1 .= "</tr>";
	}
	$html_1 .= "<input type='hidden' name='codig_and' id='codig_and' />";
	$html_1 .= "<input type='hidden' name='banco_and' id='banco_and' />";
	$html_1 .= "</table>";
	$html_1 .= "<br>";
	$html_1 .= "<br>";
}

	$table .= $html_1;

	$sel_2  = " SELECT * ";
	$sel_2 .= " FROM andamentos AS a ";
  //$sel_2 .= " JOIN banco_andamentos AS b ON a.anda_id=b.anda_id ";
	$sel_2 .= " JOIN metas_andamentos AS b ON a.anda_id=b.anda_id ";
	$sel_2 .= " WHERE b.banco_id=" . $Wb['banco_id'] . " ";
	$sel_2 .= " AND a.especie=2 ";
  //$sel_2 .= " AND b.stt='Y' ";
	$sel_2 .= " AND b.meta_mes=$mes ";
	$sel_2 .= " AND b.meta_ano=$ano ";
	$sel_2 .= " GROUP BY b.banco_id,b.anda_id ";
	$sel_2 .= " ORDER BY a.especie asc, a.ordem asc ";
	$Qsel_2 = mysqli_query($conexao4,$sel_2);
	$Wrow_2 = mysqli_num_rows($Qsel_2);
	$n2=0;
	$num_2=1;
	$col_2=0;
	$sum_mt_2=0;
	$sum_tt_2=0;
	
if($Wrow_2>0){
	if($Wrow>0){
		$html_2 .= "<table align='center' height='20%' width='100%' border='0' cellspacing='1' cellpadding='1' id='tb_fim' style='font-family:Tahoma;font-size:8pt; border-collapse: collapse;'>";
	}
	while($Wsel_2 = mysqli_fetch_array($Qsel_2)){
		$n2++;
		$tt_meta=0;
		$meta_sum=0;
		$lancamentos_2 = str_replace(",","','",$Wsel_2['anda_neo']);
		
		$Qmeta_2 = mysqli_query($conexao4,"SELECT * FROM metas_andamentos AS m WHERE m.banco_id=".$Wsel_2['banco_id']." AND m.meta_mes=" . $mes . " AND m.meta_ano=" . $ano . " AND m.anda_id = ".$Wsel_2['anda_id']." ")or die(mysqli_error());
		$Wmeta_2 = mysqli_fetch_array($Qmeta_2);
		
		$Qct_2 = mysqli_query($conexao4,"SELECT c.carteira_vinc FROM carteira AS c WHERE c.banco_id=".$Wsel_2['banco_id']." AND c.carteira_condicao='Carteira' ");
		$Wct_2 = mysqli_fetch_array($Qct_2);
		$carteira_vinc_2 = $Wct_2['carteira_vinc'];
		
		$condicao_2="";
		$Qcart_2 = mysqli_query($conexao4,"SELECT d.dados_cod,c.carteira_vinc,c.carteira_cod FROM dados AS d JOIN carteira AS c ON c.banco_id=d.banco_id WHERE d.banco_id=".$Wsel_2['banco_id']." ");
		$qtds_2 = mysqli_num_rows($Qcart_2);
		$s=0;
		while($Wcart_2 = mysqli_fetch_array($Qcart_2)){
			$s++;
			$condicao_2 .=  "'" . ($Wcart_2['dados_cod']) . "'";
			if($qtds_2>$num_2++){
				$condicao_2 .=  ",";
			}	
		}
		$num_2=1;
		$c2=0;
		$tt_2=0;
		$codig_lnc = array();
		$codig_uni = array();
		$html_2 .= "<tr style='height:30px'>";
		if($n2==1){
			$html_2 .= "<td align='center' rowspan='".($Wrow_2)."' class='cls_colun_2'><div style='color:#FFF;transform:rotate(270deg);width:20px;margin-top:20px'><b>FINANCEIRO</b></div></td>";
		}
		for($m=1;$m<=$ncol;$m++){
			if($m==1){
				$html_2 .= "<td class='cls_indic'>".$Wsel_2['nome']."</td>";				
			}else{
				
				if( (($m==2 || $m==5 || $m==8 || $m==11) && $ncol==14) || 
					(($m==2 || $m==5 || $m==8 || $m==11 || $m==14)  && $ncol==17)){
					$col_2++;
					if($Wsel_2['def_sem']=="N"){
						//calcula a meta da semana de forma automática
						$dD_2 = ($Wsem['fim_'.$col_2]-$Wsem['ini_'.$col_2]);
						$dmais_2 = (($dD_2==8?(2):($dD_2==7?(2):($dD_2==6?(1):($dD_2==5?(0):($dD_2==4?(-1):($dD_2==9?(1):"")))))));
						$diasD_2 = ($dD_2 - $dmais_2);
						////calcura se a meta está correta para acrescentar na última semana ///////
						$meta = $diasD_2 * ($Wmeta_2['meta_valor']/$diasM);
						$meta_sum += $meta;
						if( ($m==11 && $ncol==14) || ($m==14 && $ncol==17) ){
							$tt_meta = $meta_sum;
							$tt_meta = ($Wmeta_2['meta_valor']-$meta_sum)+$meta;
						}else{
							$tt_meta = $meta;
						}
						${"M_".$m} += $tt_meta;
					}else{
						//meta da semana de forma manual(fixa)
						$tt_meta = $Wsel_2['sem_'.$col_2];
						${"M_".$m} += $tt_meta;
					}
					$html_2 .= "<td align='center' class='cls_vals' style='$backg'>" . number_format($tt_meta,2,',','.') . "</td>";
					
				}else{
					if( (($m==3 || $m==6 || $m==9 || $m==12 ) && $ncol==14) ||
						(($m==3 || $m==6 || $m==9 || $m==12 || $m==15)  && $ncol==17) ){
						/////////pegando os valores depositados
						$c2++;
						$qntd_2  = " SELECT l.CodigoLancamento, l.Valor as 'qtd2'";
						$qntd_2 .= " FROM v_Processo AS p WITH (NOLOCK) ";
						$qntd_2 .= " JOIN v_Lancamento_Processo AS l ON l.CodigoProcesso=p.CodigoProcesso ";
						$qntd_2 .= " WHERE l.TipoLancamento IN ('".$lancamentos_2."') "; 
						if($carteira_vinc_2=="LIKE"){
							$qntd_2 .= " AND p.Carteira LIKE '%".str_replace("'","",$condicao_2)."%' ";
						}else{
							$qntd_2 .= " AND p.Carteira IN (".$condicao_2.") ";
						}
						if( $Wsem['ini_'.$c2] != "" && $Wsem['fim_'.$c2] !=""){
							$qntd_2 .= " AND (day(l.DataHora_Evento)>=" . $Wsem['ini_'.$c2] . " AND day(l.DataHora_Evento)<=" . $Wsem['fim_'.$c2] . ")";
						}else{
							$qntd_2 .= " AND (day(l.DataHora_Evento)>=" . date('d', strtotime(P_semana($mes,$ano,$c2,"ini"))) . " AND day(l.DataHora_Evento)<=" . date('d', strtotime(P_semana($mes,$ano,$c2,"fim"))) . ")";
						}
						$qntd_2 .= " AND MONTH(l.DataHora_Evento)=$mes ";
						$qntd_2 .= " AND YEAR(l.DataHora_Evento)=$ano ";
						$qntd_2 .= " GROUP BY l.CodigoLancamento, l.Valor ";
						
						//$html_2 .= ($qntd_2) . "<br>";
						
						$Qqntd_2 = sqlsrv_query($conexao1,$qntd_2);
						$Wbanc_qtd = 0;
						while($Wbanc_2 = sqlsrv_fetch_array($Qqntd_2, SQLSRV_FETCH_ASSOC)){
							$Wbanc_qtd  += $Wbanc_2['qtd2'];
							$codig_lnc[] = $Wbanc_2['CodigoLancamento'];
							$codig_uni[] = $Wbanc_2['CodigoLancamento'];
						}
						${"M_".$m} += $Wbanc_qtd;
						$html_2 .= "<td align='center' class='cls_vals cls_real' onclick='send_form(\"" . implode(',',$codig_uni)."\",\"" . $Wb['banco_name'] . "\",\"fat\");'>".number_format(($Wbanc_qtd?$Wbanc_qtd:0),2,',','.')."</td>";
						$codig_uni=array();
						//SOMA PARA O TOTAL
						$tt_2 += $Wbanc_qtd;
						
						$sum_tt_2 += $Wbanc_qtd;
					}else{
						$resul = number_format(($tt_meta!=0?($Wbanc_qtd/($tt_meta)):0)*100,0,',','');
						if($resul>=100 && $resul<110){
							$bol = "circle_green.png";
						}elseif($resul<100 && $resul>=80){
							$bol = "circle_yellow.png";
						}elseif($resul>=110){
							$bol = "circle_blue.png";
						}elseif($tt_meta==0){
							$bol = "circle_grey.png";
						}else{
							$bol = "circle_red.png";
						}
						if($m==$ncol){
							$tot_2 = number_format(($Wmeta_2['meta_valor']!=0?($tt_2/$Wmeta_2['meta_valor']):0)*100,0,',','');
							if($tot_2>=100 && $tot_2<110){
								$cor_2 = "circle_green.png";
							}elseif($tot_2<100 && $tot_2>=80){
								$cor_2 = "circle_yellow.png";
							}elseif($tot_2>=110){
								$cor_2 = "circle_blue.png";
							}elseif($Wmeta_2['meta_valor']==0){
								$cor_2 = "circle_grey.png";
							}else{
								$cor_2 = "circle_red.png";
							}
							
							//${"M1_".$m} += $Wmeta_2['meta_valor'];
							${"M1_".$m} += number_format($Wmeta_2['meta_valor'],2,'.','');
							${"M2_".$m} += $tt_2;
							$html_2 .= "<td align='center' class='cls_vals cls_real cls_bk' style='background:#F2F5A9' ><b>".number_format($Wmeta_2['meta_valor'],2,',','.')."</b></td>";
							$html_2 .= "<td align='center' class='cls_vals cls_real cls_bk' onclick='send_form(\"" . implode(',',$codig_lnc) ."\",\"" . $Wb['banco_name'] . "\",\"fat\");'><b>".number_format($tt_2,2,',','.')."</b></td>";
							$html_2 .= "<td align='center' class='cls_vals cls_bk' ><img src='http://10.81.11.202/img/$cor_2' class='box' />".$tot_2."%</td>";
						$codig_lnc=array();
						}else{
							$html_2 .= "<td align='center' class='cls_vals'><img src='http://10.81.11.202/img/$bol' class='box' />".number_format(($tt_meta!=0?($Wbanc_qtd/($tt_meta)):0)*100,0,',','')."%</td>";
						}
					}
				}
			}
		}
		$sum_mt_2 += $Wmeta_2['meta_valor'];
		$col_2=0;
		///EXIBINDO OS TOTAIS//////////
		
		$html_2 .= "</tr>";
		if($Wrow_2==$n2){
		$html_2 .= "<tr height='5px'>";
		//$html_2 .= "<td colspan='17' style='border: 0px'></td>";
		$html_2 .= "</tr>";
			$html_2 .= "<tr>";
			for($m=1;$m<=($ncol);$m++){
				if($m==1){
					$html_2 .= "<td style='border: 0px'></td>";
					$html_2 .= "<td class='cls_vals2 cls_bk'><b>TOTAL FINANCEIRO</b></td>";
				}else{
					if($m==$ncol){
						$m1 = ${"M1_".$m};
						$m2 = ${"M2_".$m};
						$mt = number_format((($m2/$m1)*100),1,',','');
						if($mt>=100 && $mt<110){
							$Mbol = "circle_green.png";
						}elseif($mt<100 && $mt>=80){
							$Mbol = "circle_yellow.png";
						}elseif($mt>=110){
							$Mbol = "circle_blue.png";
						}else{
							$Mbol = "circle_red.png";
						}
						
						$html_2 .= "<td align='center' class='cls_vals2 cls_bk' style='background:#F2F5A9'><b>".number_format($m1,2,',','.')."</b></td>";
						$html_2 .= "<td align='center' class='cls_vals2 cls_bk'><b>".number_format($m2,2,',','.')."</b></td>";
						$html_2 .= "<td align='center' class='cls_vals2 cls_bk' ><img src='http://10.81.11.202/img/$Mbol' class='box' />&nbsp;<b>".$mt."%</td>";
					}else{
						if( (($m==2 || $m==5 || $m==8 || $m==11) && $ncol==14) || 
							(($m==2 || $m==5 || $m==8 || $m==11 || $m==14)  && $ncol==17)){
							$Tm = (${"M_".$m});
							$html_2 .= "<td align='center' class='cls_vals2 cls_bk' style='background:#F2F5A9'><b>".number_format(${"M_".$m},2,',','.')."</b></td>";
						}elseif( (($m==3 || $m==6 || $m==9 || $m==12 ) && $ncol==14) ||
						(($m==3 || $m==6 || $m==9 || $m==12 || $m==15)  && $ncol==17) ){
							$Tr = (${"M_".$m});
							$html_2 .= "<td align='center' class='cls_vals2 cls_bk' ><b>".number_format(${"M_".$m},2,',','.')."</b></td>";
							
						}else if( (($m==4 || $m==7 || $m==10 || $m==13) && $ncol==14) || 
							(($m==4 || $m==7 || $m==10 || $m==13 || $m==16)  && $ncol==17)){
							$Tp = number_format((($Tr/$Tm)*100),1,',','');
							if($Tp>=100 && $Tp<110){
								$Tbol = "circle_green.png";
							}elseif($Tp<100 && $Tp>=80){
								$Tbol = "circle_yellow.png";
							}elseif($Tp>=110){
								$Tbol = "circle_blue.png";
							}else{
								$Tbol = "circle_red.png";
							}
							$html_2 .= "<td align='center' class='cls_vals2 cls_bk'><img src='http://10.81.11.202/img/$Tbol' class='box' />&nbsp;<b>".$Tp."%</b></td>";
						}
					}
				}
			}
			$html_2 .= "</tr>";
		}
	}
	$html_2 .= "<input type='hidden' name='codig_lnc' id='codig_lnc' />";
	$html_2 .= "<input type='hidden' name='banco_lnc' id='banco_lnc' />";
	$html_2 .= "</table>";
	
	$table .= $html_2;
	
	$html_3 .= "<br>";
	$html_3 .= "<table align='center' height='6%' width='25%' border='1' cellspacing='3' cellpadding='3' id='tb_tot' style='font-family:arial;font-size:8pt; border-collapse: collapse;'>";
		$tot_3 = number_format(($sum_tt_2/$sum_mt_2)*100,1,',','');
		if($tot_3>=100 && $tot_3<110){
			$cor_3 = "circle_green.png";
		}elseif($tot_3<100 && $tot_3>=80){
			$cor_3 = "circle_yellow.png";
		}elseif($tot_3>=110){
			$cor_3 = "circle_blue.png";
		}else{
			$cor_3 = "circle_red.png";
		}
		$html_3 .= "<tr>";
		$html_3 .= "<td align='center' style='$backg'>R$ ";
		$html_3 .= number_format($sum_mt_2,2,',','.') . "<br>";
		$html_3 .= "</td>";
		$html_3 .= "<td align='center' style='background:#ffffff'>R$ ";
		$html_3 .= number_format($sum_tt_2,2,',','.') . "<br>";
		$html_3 .= "</td>";
		$html_3 .= "<td align='center' style='background:#ffffff'><img src='http://10.81.11.202/img/$cor_3' class='box' />";
		$html_3 .= $tot_3 . "%";
		$html_3 .= "</td>";
	$html_3 .= "</tr>";
	$html_3 .= "</table>";
	$html_3 .= "<br>";
	$html_3 .= "<br>";
	$html_3 .= "<br>";
	$html_3 .= "<br>";
	
}
	$table .= $html_3;
	echo $table;
?>
<script>
	//content-box
	var altura = 0;
	altura = parseInt($("#tb_pro").height())+ parseInt($("#tb_fim").height())+parseInt($("#tb_tot").height())+200;
	//alert(altura);
	$("#content-box").css("height",altura);
</script>