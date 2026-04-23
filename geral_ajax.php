<meta http-equiv='content-type' content='text/html; charset=utf-8'>
	
<?php 

protegePagina(0);

$banco = $_POST['hid_flag'];
$startDate = $_POST['startDate']?$_POST['startDate']:date('M');
$startSetor = $_POST['startSetor']?$_POST['startSetor']:"";
$mes = $_POST['mes']?$_POST['mes']:date('m');
$ano = $_POST['ano']?$_POST['ano']:date('Y');
$tds = 0;

$Sb = "select b.banco_id,b.banco_cod from bancos as b where b.banco_id='" . $banco . "' and b.banco_status='Y' ";
$Qb = mysqli_query($conexao4,$Sb);
$Wb = mysqli_fetch_array($Qb);

$Ssem = "select * from semanas as s where s.mes='" . $mes . "' and s.ano='" . $ano . "' limit 1 ";
$Qsem = mysqli_query($conexao4,$Ssem);
$Wsem = mysqli_fetch_array($Qsem);
///define as semanas///
if($Wsem['ini_5']==0 || $Wsem['fim_5']==0){
	$nsem=4; 
	$ncol=12;
}else{
	$nsem=5; 
	$ncol=15;
}

$meses 	 = array("01"=>31,"02"=>28,"03"=>31,"04"=>30,"05"=>31,"06"=>30,"07"=>31,"08"=>31,"09"=>30,"10"=>31,"11"=>30,"12"=>31);
$fmes = $meses[$mes];

$data_ini = "01/$mes/$ano";
//if($mes.$ano>date("mY"))
if(($ano.$mes)<date("Ym")){
	$data_fim = "$fmes/$mes/$ano";
}else{
	$data_fim = date("d/m/Y");
}
$data_fms = "$fmes/$mes/$ano";

//$uteis_atu = diasUteis($data_ini,$data_fim);
//$uteis_mes = diasUteis($data_ini,$data_fms);

/////contagem dos dias úteis////
$n=0;
//////////////////////////////////

$sel  = " SELECT * ";
$sel .= " FROM bancos AS b ";
$sel .= " where 1=1 ";
$sel .= " and b.banco_status='Y' ";
if($usu_setor!=0){
	$sel .= " and banco_area = '$usu_setor'";
	///pega o nome da área
	$Sa = "select * from area as a where a.area_id='" . $usu_setor . "' ";
	$Qa = mysqli_query($conexao4,$Sa);
	$Wa = mysqli_fetch_array($Qa);
	$tit_are = " | Setor: <b>" . $Wa['area_nome'] . "</b>";
}elseif($startSetor!=""){
	///pega o nome da área
	$Sa = "select * from area as a where a.area_id='" . $startSetor . "' ";
	$Qa = mysqli_query($conexao4,$Sa);
	$Wa = mysqli_fetch_array($Qa);
	$tit_are = " Setor: <b>" . $Wa['area_nome'] . "</b>";
	$sel .= " and banco_area = '$startSetor'";
}else{
	$tit_are = " Todas as Áreas";
}

if($usu_Cliente!=0){
	$sel .= " and banco_id in (".$usu_Cliente.")";
}
$sel .= " ORDER BY b.banco_name asc ";

$Qsel = mysqli_query($conexao4,$sel);
$Wrow = mysqli_num_rows($Qsel);

$table = "";
$html_1 = "";
$mt_mes = 0;
$valor_3 = 0;
$valor_4 = 0;
$querys = "";
$num=1;
$ln=0;
$lin=0;

$tt_mt_mes = 0;
$tt_m_hoje = 0;
$tt_realiz = 0;

echo "<button name='geral' value='1' style='float:right; position:relative;margin-right:50px;border:1px dotted #999;'>Mensal</button><br>";
echo "<br><div style='font-family:arial;margin-left:40px;font-size:10pt;'>$tit_are | Mês / Ano: <b>$startDate</b> </div><br>";

$backg = "background:#F0F0F0";
?>
<script>
	function send_form(valor1,valor2){
		$("#codig_lnc").val(valor1);
		$("#banco_lnc").val(valor2);
		$("#form_ars").attr("action","dados_fatur.php");
		$("#form_ars").attr("target","_blank");
		$("#form_ars").submit();
	}
</script>
<style>
td.cls_dados{
	border-left-width: 1px;
	border:1px dotted #999;
	height:30px;
}
td.cls_body{
	border-left-width: 1px;
	border:1px dotted #999;
	height:30px;
	text-align:right;
	padding-right:5px;
}

.cls_dados{
	background:#DBDBDB;
}
.cls_sema{
	background: #ccc;
	font-weight:bold;
	border:1px dotted #999;
}
.box{
	margin-left:5px;
	float:left;
}
.cls_vals{
	width:<?php echo ($ncol==12?" 4%":" 5%"); ?>;
}
.cls_vals2{
	height:25px;
	width:<?php echo ($ncol==12?" 4%":" 5%"); ?>;
	border:1px dotted #999;
	text-align:right;
	padding-right:5px;
}
.cls_indic{
	height:30px;
	width:13%;
	border:1px dotted #999;
}
.cls_indic2{
	height:30px;
	width:3%;
	border:1px dotted #999;
}
.cls_perc{
	width:3%;
}
.cls_perc2{
	width:0.5%;
}
.cls_real{
	font-weight:bold;
	color:#8B4513; 
	cursor:pointer;
}
.cls_real:hover{
	background:#ebebeb;
}
.cls_bk{
	background:#F5F6CE;
}
.cls_bk2{
	background:#F2F3C5;
}
</style>
<?php 
$html_1 .= "<table align='center' height='50%' width='99%' border='0' cellspacing='3' cellpadding='3' style='font-family:arial;font-size:8pt; border-collapse: collapse;background:#ffffff'>";
	$html_1 .= "<tr>";
		$html_1 .= "<td align='center' rowspan='2' class='cls_sema cls_indic'>CLIENTES</td>";
		if($Wsem['ini_1'] != "" && $Wsem['fim_1'] !=""){
			$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . $Wsem['ini_1'] . " à " . $Wsem['fim_1'] . "</td>";
			$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . $Wsem['ini_2'] . " à " . $Wsem['fim_2'] . "</td>";
			$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . $Wsem['ini_3'] . " à " . $Wsem['fim_3'] . "</td>";
			$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . $Wsem['ini_4'] . " à " . $Wsem['fim_4'] . "</td>";
			if($ncol==15){
				$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . $Wsem['ini_5'] . " à " . $Wsem['fim_5'] . "</td>";
			}
			$html_1 .= "<td STYLE='width:0.5%' > </td>";
			$html_1 .= "<td align='center' colspan='3' rowspan='1' class='cls_sema cls_vals'>TOTAL</td>";
			$dia_s[] = (date('d', strtotime(P_semana($mes,$ano,1,"fim"))) - date('d', strtotime(P_semana($mes,$ano,1,"ini"))));
			$dia_s[] = (date('d', strtotime(P_semana($mes,$ano,2,"fim"))) - date('d', strtotime(P_semana($mes,$ano,2,"ini"))));
			$dia_s[] = (date('d', strtotime(P_semana($mes,$ano,3,"fim"))) - date('d', strtotime(P_semana($mes,$ano,3,"ini"))));
			$dia_s[] = (date('d', strtotime(P_semana($mes,$ano,4,"fim"))) - date('d', strtotime(P_semana($mes,$ano,4,"ini"))));
			$dia_s[] = (date('d', strtotime(P_semana($mes,$ano,5,"fim"))) - date('d', strtotime(P_semana($mes,$ano,5,"ini"))));
			$diasM=0;
			foreach($dia_s as $d){
				$dm = (($d==8?(3):($d==7?(2):($d==6?(1):""))));
				$diasM += ($d - $dm);
			}
		}else{
			$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . date('d', strtotime(P_semana($mes,$ano,1,"ini"))). " a " . date('d', strtotime(P_semana($mes,$ano,1,"fim"))) . "</td>";
			$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . date('d', strtotime(P_semana($mes,$ano,2,"ini"))). " a " . date('d', strtotime(P_semana($mes,$ano,2,"fim"))) . "</td>";
			$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . date('d', strtotime(P_semana($mes,$ano,3,"ini"))). " a " . date('d', strtotime(P_semana($mes,$ano,3,"fim"))) . "</td>";
			$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . date('d', strtotime(P_semana($mes,$ano,4,"ini"))). " a " . date('d', strtotime(P_semana($mes,$ano,4,"fim"))) . "</td>";
			if($ncol==15){
				$html_1 .= "<td align='center' colspan='3' class='cls_sema'>" . date('d', strtotime(P_semana($mes,$ano,5,"ini"))) . " a " . date('d', strtotime(P_semana($mes,$ano,5,"fim"))) . "</td>";
			}
			$html_1 .= "<td align='center' colspan='3' rowspan='1' class='cls_sema2 cls_vals'>TOTAL		</td>";
		}
	$html_1 .= "</tr>";
	$html_1 .= "<tr>";
		$html_1 .= "<td align='center' class='cls_dados cls_vals' >META</td>";
		$html_1 .= "<td align='center' class='cls_dados cls_vals' >REALIZADO</td>";
		$html_1 .= "<td align='center' class='cls_dados cls_vals' >PERC.</td>";
		$html_1 .= "<td align='center' class='cls_dados cls_vals' >META</td>";
		$html_1 .= "<td align='center' class='cls_dados cls_vals' >REALIZADO</td>";
		$html_1 .= "<td align='center' class='cls_dados cls_vals' >PERC.</td>";
		
		$html_1 .= "<td align='center' class='cls_dados cls_vals' >META</td>";
		$html_1 .= "<td align='center' class='cls_dados cls_vals' >REALIZADO</td>";
		$html_1 .= "<td align='center' class='cls_dados cls_vals' >PERC.</td>";
		
		$html_1 .= "<td align='center' class='cls_dados cls_vals' >META</td>";
		$html_1 .= "<td align='center' class='cls_dados cls_vals' >REALIZADO</td>";
		$html_1 .= "<td align='center' class='cls_dados cls_vals' >PERC.</td>";
		if($ncol==15){
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >META		</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >REALIZADO</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_vals' >FAROL	</td>";
			
			$html_1 .= "<td class='' > </td>";
			$html_1 .= "<td align='center' class='cls_dados cls_bk2' >META		</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_bk2' >REALIZADO</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_bk2' >FAROL	</td>";
		}else{
			$html_1 .= "<td class='' > </td>";
			$html_1 .= "<td align='center' class='cls_dados cls_bk2' >META		</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_bk2' >REALIZADO</td>";
			$html_1 .= "<td align='center' class='cls_dados cls_bk2' >FAROL	</td>";
		}
	$html_1 .= "</tr>";
$m1=0;
$m2=0;
while($Wsel = mysqli_fetch_array($Qsel)){
	$lin++;
	$html_1 .= "<tr style='height:30px'>";
	$html_1 .= "<td class='cls_indic' style='padding-left:5px'>" . $Wsel['banco_name'] . " (" . $Wsel['banco_class'] . ") </td>";
	
	$Qand = mysqli_query($conexao4,"SELECT * FROM metas_andamentos AS m JOIN andamentos AS a ON m.anda_id=a.anda_id WHERE m.banco_id='".$Wsel['banco_id']."' AND m.meta_mes=" . $mes . " AND m.meta_ano=" . $ano . " AND a.especie=2 GROUP BY a.anda_id, m.meta_valor ");
	
	$lancamentos="";
	$sem_1 = 0;
	$sem_2 = 0;
	$sem_3 = 0;
	$sem_4 = 0;
	$sem_5 = 0;
	while($Wand = mysqli_fetch_array($Qand)){
		$mt_mes += $Wand['meta_valor'];
		if(($ln++)>0){
			$lancamentos .= ",";
		}
		$lancamentos .= $Wand['anda_neo'];
		$sem_1 += $Wand['sem_1'];
		$sem_2 += $Wand['sem_2'];
		$sem_3 += $Wand['sem_3'];
		$sem_4 += $Wand['sem_4'];
		$sem_5 += $Wand['sem_5'];
	}
	
	//$vl_unt = ($mt_mes/$uteis_mes);
	$c=0;
	$tt=0;
	for($m=1;$m<=$ncol;$m++){
		if( (($m==1 || $m==4 || $m==7 || $m==10) && $ncol==12) ||
			(($m==1 || $m==4 || $m==7 || $m==10 || $m==13) && $ncol==15) ){			
			$col_2++;
			$Qsemn = mysqli_query($conexao4,"SELECT * FROM metas_andamentos AS m JOIN andamentos AS a ON m.anda_id=a.anda_id WHERE m.banco_id='".$Wsel['banco_id']."' AND m.meta_mes=" . $mes . " AND m.meta_ano=" . $ano . " AND a.especie=2 GROUP BY a.anda_id, m.meta_valor ");
			if(mysqli_num_rows($Qsemn)>0){
				$Wsemn = mysqli_fetch_array($Qsemn);
				if($Wsemn['def_sem']=="N"){
					$dD_2 = ($Wsem['fim_'.$col_2]-$Wsem['ini_'.$col_2]);
					$dmais_2 = (($dD_2==8?(2):($dD_2==7?(2):($dD_2==6?(1):($dD_2==5?(0):($dD_2==4?(-1):($dD_2==9?(1):"")))))));
					$diasD_2 = ($dD_2 - $dmais_2);
					////calcura se a meta está correta para acrescentar na última semana ///////
					$meta = $diasD_2*($mt_mes/$diasM);
				}elseif($Wsemn['def_sem']=="Y"){
					$meta=number_format((${'sem_'.$col_2}),0,"","");
				}
				${"M_".$m} += $meta;
				$html_1 .= "<td class='cls_vals cls_body' align='center' style='$backg'>" . number_format($meta,2,",",".") . "</td>";
			}else{
				$html_1 .= "<td class='cls_vals cls_body' align='center' style='$backg'>0,00</td>";
			}
		}else{
			if( (($m==2 || $m==5 || $m==8 || $m==11) && $ncol==12) ||
				(($m==2 || $m==5 || $m==8 || $m==11 || $m==14)  && $ncol==15) ){
				////////
				$condicao="";
				$c++;
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
				$Wbanc_qtd = 0;
				$codig_lnc = array();
				if($lancamentos!=""){
					////////honorários neo jurídico//////////////////
					$querys  = " SELECT l.CodigoLancamento, l.Valor ";
					$querys .= " FROM v_Processo AS p WITH (NOLOCK) ";
					$querys .= " JOIN v_Lancamento_Processo AS l ON l.CodigoProcesso=p.CodigoProcesso ";
					$querys .= " AND p.Carteira IN ( " . $condicao . " ) ";  
					$querys .= " AND l.TipoLancamento IN ('".(str_replace(",","','",$lancamentos))."') ";  
					//$querys .= " AND l.StatusLancamento IN ('Pago pela Assessoria','Pendente na Assessoria','Enviado ao Contratante','Aprovado pelo Contratante','Pago pelo Contratante') ";
					if( $Wsem['ini_'.$c] != "" && $Wsem['fim_'.$c] !=""){
						$querys .= " AND (day(l.DataHora_Evento)>=" . $Wsem['ini_'.$c] . " AND day(l.DataHora_Evento)<=" . $Wsem['fim_'.$c] . ")";
					}else{
						$querys .= " AND (day(l.DataHora_Evento)>=" . date('d', strtotime(P_semana($mes,$ano,$c,"ini"))) . " AND day(l.DataHora_Evento)<=" . date('d', strtotime(P_semana($mes,$ano,$c,"fim"))) . ")";
					}
					$querys .= " AND MONTH(l.DataHora_Evento)=$mes ";
					$querys .= " AND YEAR(l.DataHora_Evento)=$ano ";
					$querys .= " GROUP BY l.CodigoLancamento, l.Valor ";
					// echo ($querys) . "<hr>";
					$Qqntd = sqlsrv_query($conexao1,$querys) or die(print_r(sqlsrv_errors()));
					while($Wbanc_2 = sqlsrv_fetch_array($Qqntd, SQLSRV_FETCH_ASSOC)){
						$Wbanc_qtd += $Wbanc_2['Valor'];
						$codig_lnc[] = $Wbanc_2['CodigoLancamento'];
					}
				}
				//SOMA PARA O TOTAL
				$tt += $Wbanc_qtd;
				${"R_".$m} += $Wbanc_qtd;
				$html_1 .= "<td class='cls_vals cls_body cls_real' align='center' onclick='send_form(\"" . implode(',',$codig_lnc)."\",\"" . $Wsel['banco_name'] . "\");'>" . number_format($Wbanc_qtd,2,",",".") . "</td>";
			}else{
				$resul = number_format(($meta!=0?(($Wbanc_qtd/$meta)*100):0),0,",",".");
							
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
				$html_1 .= "<td class='cls_body' align='center'><img src='http://10.81.11.202/img/$bol' class='box' />" . $resul . " %</td>";
			}
		}
	}
	$col_2=0;
	$tot = number_format(($mt_mes!=0?($tt/($mt_mes)):0)*100,1,',','');
	if($tot>=100 && $tot<110){
		$cor = "circle_green.png";
	}elseif($tot<100 && $tot>=80){
		$cor = "circle_yellow.png";
	}elseif($tot>=110){
		$cor = "circle_blue.png";
	}elseif($tot==0){
		$cor = "circle_grey.png";
	}else{
		$cor = "circle_red.png";
	}
	
	$m1 += $mt_mes;
	$m2 += $tt;
							
	$html_1 .= "<td class=''>&nbsp;</td>";	
	$html_1 .= "<td class='cls_body cls_bk' align='center' style='background:#F2F5A9'><b>".number_format($mt_mes,2,',','.')."</b></td>";	
	$html_1 .= "<td class='cls_body cls_bk' align='center' style='color:#000'><b>".number_format($tt,2,',','.')."</b></td>";
	$html_1 .= "<td class='cls_body cls_bk' align='center' style='color:#000'><img src='http://10.81.11.202/img/$cor' class='box' />".number_format(($mt_mes!=0?($tt/($mt_mes)):0)*100,0,',','')." %</td>";
	
	$html_1 .= "</tr>";
	
	$tt_mt_mes += $mt_mes;
	$tt_m_hoje += $m_hoje;
	$tt_realiz += $valor_3;
	
	$mt_mes = 0;
	$m_hoje = 0;
	$querys="";
	
	
	
	///EXIBINDO OS TOTAIS//////////
	if($Wrow==$lin){
		$html_1 .= "<tr height='5px'>";
		$html_1 .= "</tr>";
		$html_1 .= "<tr>";
		$html_1 .= "<td class='cls_bk' style='background:#F2F3C5;border:1px dotted #999;'><b>TOTAL</b></td>";
		for($m=1;$m<=($ncol);$m++){
			if( (($m==1 || $m==4 || $m==7 || $m==10) && $ncol==12) ||
				(($m==1 || $m==4 || $m==7 || $m==10 || $m==13) && $ncol==15) ){	
					$Tm = (${"M_".$m});
					$html_1 .= "<td align='center' class='cls_vals2 cls_bk' style='background:#F2F5A9'><b>".number_format(${"M_".$m},2,',','.')."</b></td>";
			}elseif( (($m==2 || $m==5 || $m==8 || $m==11) && $ncol==12) ||
					 (($m==2 || $m==5 || $m==8 || $m==11 || $m==14)  && $ncol==15) ){
					$Tr = (${"M_".$m});
					$html_1 .= "<td align='center' class='cls_vals2 cls_bk' ><b>".number_format(${"R_".$m},2,',','.')."</b></td>";
				
			}elseif( (($m==3 || $m==6 || $m==9 || $m==12 ) && $ncol==12) ||
					 (($m==3 || $m==6 || $m==9 || $m==12 || $m==15)  && $ncol==15) ){
						$Tp = number_format((($Tr/$Tm)*100),0,',','');
						if($Tp>=100 && $Tp<110){
							$Tbol = "circle_green.png";
						}elseif($Tp<100 && $Tp>=80){
							$Tbol = "circle_yellow.png";
						}elseif($Tp>=110){
							$Tbol = "circle_blue.png";
						}elseif($Tp==0){
							$Tbol = "circle_grey.png";
						}else{
							$Tbol = "circle_red.png";
						}
						$html_1 .= "<td align='center' class='cls_vals2 cls_bk'><img src='http://10.81.11.202/img/$Tbol' class='box' /><b>".$Tp." %</b></td>";
			}
		}
		$mt = number_format((($m2/$m1)*100),0,',','');
		if($mt>=100 && $mt<110){
			$Mbol = "circle_green.png";
		}elseif($mt<100 && $mt>=80){
			$Mbol = "circle_yellow.png";
		}elseif($mt>=110){
			$Mbol = "circle_blue.png";
		}elseif($mt==0){
			$Mbol = "circle_grey.png";
		}else{
			$Mbol = "circle_red.png";
		}
		
		$html_1 .= "<td class=''>&nbsp;</td>";	
		$html_1 .= "<td align='center' class='cls_vals2 cls_bk' style='background:#F2F5A9'><b>".number_format($m1,2,',','.')."</b></td>";
		$html_1 .= "<td align='center' class='cls_vals2 cls_bk'><b>".number_format($m2,2,',','.')."</b></td>";
		$html_1 .= "<td align='center' class='cls_vals2 cls_bk' ><img src='http://10.81.11.202/img/$Mbol' class='box' /><b>".$mt." %</td>";
		$html_1 .= "</tr>";
	
	}
}

$html_1 .= "<input type='hidden' name='codig_lnc' id='codig_lnc' />";
$html_1 .= "<input type='hidden' name='banco_lnc' id='banco_lnc' />";
$html_1 .= "<input type='hidden' name='startSetor' value='$startSetor'/>";
$html_1 .= "<input type='hidden' name='startDate'  value='$startDate' />";
$html_1 .= "<input type='hidden' name='mes'  value='$mes' />";
$html_1 .= "<input type='hidden' name='ano'  value='$ano' />";
$html_1 .= "</table>";
$table .= $html_1;	
echo $table;

?>
<style>
#content-box{
	height:<?php echo ($lin*30)+360; ?>px;
}
</style>