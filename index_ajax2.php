<meta http-equiv='content-type' content='text/html; charset=utf-8'>
<?php 

if($_POST['hid_flag']==""){
	echo "<br><br><br><br><br>";
	echo "<div style='font-size:18px;height:50px;text-align:center'>Volte e selecione o Banco!</div>";
	echo "<div style='font-size:18px;height:50px;text-align:center'><input type='button' onclick='javascript:window.history.back()' value='Voltar' style='cursor:pointer;height:30px;width:100px'/></div>";
	exit;
}

$banco = $_POST['hid_flag'];
$startDate = isset($_POST['startDate'])?$_POST['startDate']:date('M');
$mes = isset($_POST['mes'])?$_POST['mes']:date('m');
$ano = isset($_POST['ano'])?$_POST['ano']:date('Y');

$Sb = "select b.banco_id,b.banco_cod,b.banco_name from bancos as b where b.banco_id='" . $banco . "' ";
$Qb = mysqli_query($conexao4,$Sb);
$Wb = mysqli_fetch_array($Qb);

$Ssem = "select * from semanas as s where s.mes='" . $mes . "' and s.ano='" . $ano . "' limit 1 ";
$Qsem = mysqli_query($conexao4,$Ssem);
$Wsem = mysqli_fetch_array($Qsem);

echo "<br><div style='font-family:arial;margin-left:40px;font-size:10pt;'>Cliente: <b>" . $Wb['banco_cod'] . "</b> | Mês / Ano: <b>$startDate</b></div><br>";
///define as semanas///
if($Wsem['ini_5']==0 || $Wsem['fim_5']==0){
	$nsem=4; 
	$ncol=15;
}else{
	$nsem=5; 
	$ncol=18;
}
?>
<script>
	function send_form(valor1,valor2,valor3){		
		if(valor3=="and"){
			$("#codig_and").val(valor1);
			$("#banco_and").val(valor2);
			$("#form_ars").attr("action","dados_anda.php");
		}else if(valor3=="fat"){
			$("#codig_lnc").val(valor1);
			$("#banco_lnc").val(valor2);
			$("#form_ars").attr("action","dados_fatur.php");
		}
		$("#form_ars").attr("target","_blank");
		$("#form_ars").submit();
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
	margin-left:10px;
	float:left;
	width:10px;
	height:10px;
	border:1px solid #ccc;
	border-radius: 10px;
}
.cls_colun_2{
	width:2%;
	background: #FFB90F;
}
.cls_vals{
	width:<?php echo ($ncol==18?" 5%":" 6%"); ?>;
}
.cls_indic{
	width:13%;
	padding-left: 5px;
}
.cls_real:hover{
	background:#ebebeb;
	cursor:pointer;
}
</style>

<?php 

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
$sel .= " ORDER BY a.especie asc ";
$Qsel = mysqli_query($conexao4,$sel);
$Wrow = mysqli_num_rows($Qsel);

$num=1;
$col=0;
$backg = "background:#F0F0F0";

$html_1 = "";
$html_2 = "";
$html_3 = "";

//$hei_p = $Wrow*($Wrow>4?7:8);
	?>
	<table align="center" height="auto" width="100%" border="0" cellspacing='1' cellpadding='1' id="tb_pro" style="font-family:Tahoma;font-size:8pt; border-collapse: collapse;">
		<tr>
			<td align="center" rowspan="2" style="border:0" style="width:5px"></td>
			<td align="center" rowspan="2" class="cls_sema cls_indic">INDICADOR</td>
			<?php 
			if($Wsem['ini_1'] != "" && $Wsem['fim_1'] !=""){
				?>
				<td align="center" colspan="3" class="cls_sema"><?php echo $Wsem['ini_1']; ?> à <?php echo $Wsem['fim_1']; ?></td>
				<td align="center" colspan="3" class="cls_sema"><?php echo $Wsem['ini_2']; ?> à <?php echo $Wsem['fim_2']; ?></td>
				<td align="center" colspan="3" class="cls_sema"><?php echo $Wsem['ini_3']; ?> à <?php echo $Wsem['fim_3']; ?></td>
				<td align="center" colspan="3" class="cls_sema"><?php echo $Wsem['ini_4']; ?> à <?php echo $Wsem['fim_4']; ?></td>
				<?php 
				if($ncol==18){
					?>
					<td align="center" colspan="3" class="cls_sema"><?php echo $Wsem['ini_5']; ?> à <?php echo $Wsem['fim_5']; ?></td>
					<?php 
				}
				?>
				<td align="center" colspan="2" rowspan="2" class="cls_sema cls_vals">TOTAL		</td>
				<?php 
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
			?>
				<td align="center" colspan="3" class="cls_sema"><?php echo date('d', strtotime(P_semana($mes,$ano,1,"ini"))); ?> a <?php echo date('d', strtotime(P_semana($mes,$ano,1,"fim"))); ?></td>
				<td align="center" colspan="3" class="cls_sema"><?php echo date('d', strtotime(P_semana($mes,$ano,2,"ini"))); ?> a <?php echo date('d', strtotime(P_semana($mes,$ano,2,"fim"))); ?></td>
				<td align="center" colspan="3" class="cls_sema"><?php echo date('d', strtotime(P_semana($mes,$ano,3,"ini"))); ?> a <?php echo date('d', strtotime(P_semana($mes,$ano,3,"fim"))); ?></td>
				<td align="center" colspan="3" class="cls_sema"><?php echo date('d', strtotime(P_semana($mes,$ano,4,"ini"))); ?> a <?php echo date('d', strtotime(P_semana($mes,$ano,4,"fim"))); ?></td>
				<?php 
				if($ncol==18){
					?>
					<td align="center" colspan="3" class="cls_sema"><?php echo date('d', strtotime(P_semana($mes,$ano,5,"ini"))); ?> a <?php echo date('d', strtotime(P_semana($mes,$ano,5,"fim"))); ?></td>
					<?php 
				}
				?>
				<td align="center" colspan="2" rowspan="2" class="cls_sema2 cls_vals">TOTAL		</td>
				<?php 
			}
			?>
		</tr>
		<tr>
			<td align="center" class="cls_dados cls_vals" >META			</td>
			<td align="center" class="cls_dados cls_vals" >REALIZADO	</td>
			<td align="center" class="cls_dados cls_vals" >FAROL		</td>
			<td align="center" class="cls_dados cls_vals" >META			</td>
			<td align="center" class="cls_dados cls_vals" >REALIZADO	</td>
			<td align="center" class="cls_dados cls_vals" >FAROL		</td>
			<td align="center" class="cls_dados cls_vals" >META			</td>
			<td align="center" class="cls_dados cls_vals" >REALIZADO	</td>
			<td align="center" class="cls_dados cls_vals" >FAROL		</td>
			<td align="center" class="cls_dados cls_vals" >META			</td>
			<td align="center" class="cls_dados cls_vals" >REALIZADO	</td>
			<td align="center" class="cls_dados cls_vals" >FAROL		</td>
			<?php 
			if($ncol==18){
				?>
				<td align="center" class="cls_dados cls_vals" >META		</td>
				<td align="center" class="cls_dados cls_vals" >REALIZADO</td>
				<td align="center" class="cls_dados cls_vals" >FAROL	</td>
				<?php 
			}
			?>
		</tr>
	<?php
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
		for($m=1;$m<=$ncol;$m++){
			
			if($n==1 && $m==1){
				$html_1 .= "<td align='center' rowspan='".$Wrow."' class='cls_colun'><div style='color:#FFF;transform: rotate(270deg);width:20px'><b>OPERAÇÃO</b></div></td>";
			}else{
				if($n==1 && $m==2){
					$html_1 .= "<td class='cls_indic'>".$Wsel['nome']."</td>";
						
				}else{
					if(($n>1) && $m==1){
						$html_1 .= "<td class='cls_indic'>".$Wsel['nome']."</td>";
					}else{
						if(  ($n>1 && ($m==2 || $m==5 || $m==8 || $m==11)) || 
							 ($n>1 && ($m==2 || $m==5 || $m==8 || $m==11 || $m==14) && $ncol==18) ){
							$col++;
							
							$dD = ($Wsem['fim_'.$col]-$Wsem['ini_'.$col]);
							$dmais = (($dD==8?(2):($dD==7?(2):($dD==6?(1):($dD==5?(0):($dD==4?(-1):($dD==9?(1):"")))))));
							$diasD = ($dD - $dmais);
							$meta = number_format($diasD * ($Wmeta['meta_valor']/$diasM),0,'','');
							////calcura a meta para acrescentar no final///////
							//$meta = number_format($diasD * ($Wmeta['meta_valor']/$diasM),0,'','');
							$meta_sum += $meta;
							if($m==14 && $ncol==18){
								$tt_meta = $meta_sum;
								$tt_meta = ($Wmeta['meta_valor']-$meta_sum)+$meta;
							}elseif($m==11 && $ncol==15){
								$tt_meta = $meta_sum;
								$tt_meta = ($Wmeta['meta_valor']-$meta_sum)+$meta;
							}else{
								$tt_meta = $meta;
							}
							$html_1 .= "<td align='center' class='cls_vals' style='$backg'>" . $tt_meta . "</td>";
						}else{
							if( ($n==1 && ($m==3 || $m==6 || $m==9 || $m==12) ) || 
								($n==1 && ($m==3 || $m==6 || $m==9 || $m==12 || $m==15) && $ncol==18) ){
								$col++;
								$dD = ($Wsem['fim_'.$col]-$Wsem['ini_'.$col]);
								$dmais = (($dD==8?(2):($dD==7?(2):($dD==6?(1):($dD==5?(0):($dD==4?(-1):($dD==9?(1):"")))))));
								
								$diasD = ($dD - $dmais);
								////calcura a meta para acrescentar no final///////
								$meta = number_format($diasD * ($Wmeta['meta_valor']/$diasM),0,'','');
								$meta_sum += $meta;
								if($m==15 && $ncol==18){
									$tt_meta = $meta_sum;
									$tt_meta = ($Wmeta['meta_valor']-$meta_sum)+$meta;
								}elseif($m==12 && $ncol==15){
									$tt_meta = $meta_sum;
									$tt_meta = ($Wmeta['meta_valor']-$meta_sum)+$meta;
								}else{
									$tt_meta = $meta;
								}
								
								$html_1 .= "<td align='center' class='cls_vals' style='$backg'>" . $tt_meta . "</td>";
							}else{
								if( ($m==14 && $n>1) || 
									($m==17 && $n>1 && $ncol==18) ){
									//$html_1 .= "<td align='center'>".$Wbanc['qtd']." -".$n."</td>";
								}else{
									if( (($n==1 && ($m==4 || $m==7 || $m==10 || $m==13)) || ($n>1 && ($m==3 || $m==6 || $m==9 || $m==12)) ) || 
										(($n==1 && ($m==4 || $m==7 || $m==10 || $m==13 || $m==16) && $ncol==18) || ($n>1 && ($m==3 || $m==6 || $m==9 || $m==12 || $m==15) && $ncol==18) ) ){
										/////////pegando os valores depositados
										$c++;
										$qntd0  = " SELECT sum(1) as 'qtd' FROM Andamento_Processo AS a ";
										$qntd1  = " SELECT a.CodigoAndamento FROM Andamento_Processo AS a ";
										$qntd  = " JOIN Processo AS p ON p.CodigoProcesso=a.CodigoProcesso ";
										$qntd .= " WHERE a.TipoAndamentoProcesso IN ('".$lancamentos."') "; 
										$qntd .= " AND p.TipoProcesso NOT IN ('CARTA PRECATÓRIA') ";
										if($carteira_vinc=="LIKE"){
											$qntd .= " AND p.Carteira LIKE '%".str_replace("'","",$condicao)."%' ";
										}else{
											$qntd .= " AND p.Carteira IN (".$condicao.") ";
										}
										//if( ${"ini_" . $c} != "" && ${"fim_" . $c} !=""){
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
										$banq = 0;
										//$html_1 .= ($qntd) . "<hr>";
										//echo $andamentos[$Wsel['anda_neo']] . " ==> " . ($Wsel['anda_neo']) . "<hr>";
										$Qqntd = sqlsrv_query($conexao1,($qntd0.$qntd));
										//Wbanc$Wbanc = sqlsrv_fetch_array($Qqntd, SQLSRV_FETCH_ASSOC);
										while($Wbanc = sqlsrv_fetch_array($Qqnt)){
											$banq += $Wbanc['qtd'];
										}	
										$html_1 .= "<td align='center' class='cls_vals cls_real' onclick='send_form(\"" . implode(',',$codig_now) ."\",\"" . $Wb['banco_name'] . "\",\"and\");'>".$Wbanc['qtd']."</td>";
										$codig_now="";
										$tt += $banq;
									}else{
										$resul = number_format(($banq/$tt_meta )*100,0,',','');
										
										if($resul>=100 && $resul<110){
											$bol = "green";
										}elseif($resul<100 && $resul>=80){
											$bol = "#FFB90F";
										}elseif($resul>=110){
											$bol = "#1C86EE";
										}else{
											$bol = "red";
										}
										if($m==$ncol){
											$tot = number_format(($tt/$Wmeta['meta_valor'])*100,0,',','');
											if($tot>=100 && $tot<110){
												$cor = "green";
											}elseif($tot<100 && $tot>=80){
												$cor = "#FFB90F";
											}elseif($tot>=110){
												$cor = "#1C86EE";
											}else{
												$cor = "red";
											}
											$html_1 .= "<td align='center' class='cls_vals cls_real' onclick='send_form(\"" . implode(',',$codig_and) ."\",\"" . $Wb['banco_name'] . "\",\"and\");'><b>".$tt . "</b></td>";
											$html_1 .= "<td align='center' class='cls_vals' ><span class='box' style='background:$cor;'>&nbsp;</span>".$tot."%</td>";
											$codig_and="";
										}else{
											$html_1 .= "<td align='center' class='cls_vals'><span class='box' style='background:$bol;'>&nbsp;</span>" . number_format(($Wbanc['qtd']/($tt_meta))*100,0,',','')."%</td>";
										}
									}
								}
							}
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
	echo $html_1;
	?>
	</table>
<br>
<br>
<?php 
}
?>
<?php 
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
	$sel_2 .= " ORDER BY a.especie asc ";
	$Qsel_2 = mysqli_query($conexao4,$sel_2);
	$Wrow_2 = mysqli_num_rows($Qsel_2);
	$n2=0;
	$num_2=1;
	$col_2=0;
	$sum_mt_2=0;
	$sum_tt_2=0;
	
if($Wrow_2>0){
	if($Wrow>0){
	?>
	<table align="center" height="20%" width="100%" border="0" cellspacing='1' cellpadding='1' id="tb_fim" style="font-family:Tahoma;font-size:8pt; border-collapse: collapse;">
	<?php 
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
		
		for($m=1;$m<=$ncol;$m++){
			if($n2==1 && $m==1){
				$html_2 .= "<td align='center' rowspan='".($Wrow_2+1)."' class='cls_colun_2'><div style='color:#FFF;transform:rotate(270deg);width:20px;margin-top:20px'><b>FINANCEIRO</b></div></td>";
			}else{
				if($n2==1 && $m==2){
					$html_2 .= "<td class='cls_indic'>".$Wsel_2['nome']."</td>";
						
				}else{
					if(($n2>1) && $m==1){
						$html_2 .= "<td class='cls_indic'>".$Wsel_2['nome']."</td>";
					}else{
						if(  ($n2>1 && ($m==2 || $m==5 || $m==8 || $m==11)) || 
							 ($n2>1 && ($m==2 || $m==5 || $m==8 || $m==11 || $m==14) && $ncol==18) ){
						
						//if($n2>1 && ($m==2 || $m==5 || $m==8 || $m==11)){
							$col_2++;
							$dD_2 = ($Wsem['fim_'.$col_2]-$Wsem['ini_'.$col_2]);
							$dmais_2 = (($dD_2==8?(2):($dD_2==7?(2):($dD_2==6?(1):($dD_2==5?(0):($dD_2==4?(-1):($dD_2==9?(1):"")))))));
							$diasD_2 = ($dD_2 - $dmais_2);
							////calcura se a meta está correta para acrescentar na última semana ///////
							$meta = $diasD_2 * ($Wmeta_2['meta_valor']/$diasM);
							$meta_sum += $meta;
							if($m==14 && $ncol==18){
								$tt_meta = $meta_sum;
								$tt_meta = ($Wmeta_2['meta_valor']-$meta_sum)+$meta;
							}elseif($m==11 && $ncol==15){
								$tt_meta = $meta_sum;
								$tt_meta = ($Wmeta_2['meta_valor']-$meta_sum)+$meta;
							}else{
								$tt_meta = $meta;
							}
							
							$html_2 .= "<td align='center' class='cls_vals' style='$backg'>R$ " . number_format($tt_meta,2,',','.') . "</td>";
						}else{
							if( ($n2==1 && ($m==3 || $m==6 || $m==9 || $m==12) ) || 
								($n2==1 && ($m==3 || $m==6 || $m==9 || $m==12 || $m==15) && $ncol==18) ) {
								$col_2++;
								$dD_2 = ($Wsem['fim_'.$col_2]-$Wsem['ini_'.$col_2]);
								$dmais_2 = (($dD_2==8?(2):($dD_2==7?(2):($dD_2==6?(1):($dD_2==5?(0):($dD_2==4?(-1):($dD_2==9?(1):"")))))));
								$diasD_2 = ($dD_2 - $dmais_2);
								////calcura se a meta está correta para acrescentar na última semana ///////
								$meta = $diasD_2 * ($Wmeta_2['meta_valor']/$diasM);
								$meta_sum += $meta;
								if($m==15 && $ncol==18){
									$tt_meta = $meta_sum;
									$tt_meta = ($Wmeta_2['meta_valor']-$meta_sum)+$meta;
								}elseif($m==12 && $ncol==15){
									$tt_meta = $meta_sum;
									$tt_meta = ($Wmeta_2['meta_valor']-$meta_sum)+$meta;
								}else{
									$tt_meta = $meta;
								}
								
								$html_2 .= "<td align='center' class='cls_vals' style='$backg'>R$ " . number_format($tt_meta,2,',','.') ."</td>";
							}else{
								if( ($m==14 && $n2>1) || 
									($m==17 && $n2>1 && $ncol==18) ){
									//$html_2 .= "<td align='center'>".$Wbanc['qtd']." -".$n2."</td>";
								}else{
									if( ( ($n2==1 && ($m==4 || $m==7 || $m==10 || $m==13)) || ($n2>1 && ($m==3 || $m==6 || $m==9 || $m==12)) ) || 
										( ($n2==1 && ($m==4 || $m==7 || $m==10 || $m==13 || $m==16) && $ncol==18) || ($n2>1 && ($m==3 || $m==6 || $m==9 || $m==12 || $m==15) && $ncol==18) ) ){
										/////////pegando os valores depositados
										$c2++;
										$qntd_2  = " SELECT l.CodigoLancamento, l.Valor as 'qtd2'";
										$qntd_2 .= " FROM Processo AS p ";
										$qntd_2 .= " JOIN Lancamento_Processo AS l ON l.CodigoProcesso=p.CodigoProcesso ";
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
										
										//$html_2 .= $qntd_2 . "<br>";
										
										$Qqntd_2 = sqlsrv_query($conexao1,$qntd_2);
										$Wbanc_qtd = 0;
										while($Wbanc_2 = sqlsrv_fetch_array($Qqntd_2, SQLSRV_FETCH_ASSOC)){
											$Wbanc_qtd += $Wbanc_2['qtd2'];
											$codig_lnc[] = $Wbanc_2['CodigoLancamento'];
											$codig_uni[] = $Wbanc_2['CodigoLancamento'];
										}
										$html_2 .= "<td align='center' class='cls_vals cls_real' onclick='send_form(\"" . implode(',',$codig_uni)."\",\"" . $Wb['banco_name'] . "\",\"fat\");'>R$ ".number_format(($Wbanc_qtd?$Wbanc_qtd:0),2,',','.')."</td>";
										$codig_uni="";
										//SOMA PARA O TOTAL
										$tt_2 += $Wbanc_qtd;
										
										$sum_tt_2 += $Wbanc_qtd;
									}else{
										$resul = number_format(($tt_meta!=0?($Wbanc_qtd/($tt_meta)):0)*100,0,',','');
										if($resul>=100 && $resul<110){
											$bol = "green";
										}elseif($resul<100 && $resul>=80){
											$bol = "#FFB90F";
										}elseif($resul>=110){
											$bol = "#1C86EE";
										}else{
											$bol = "red";
										}
										if($m==$ncol){
											
											$tot_2 = number_format(($Wmeta_2['meta_valor']!=0?($tt_2/$Wmeta_2['meta_valor']):0)*100,0,',','');
											if($tot_2>=100 && $tot_2<110){
												$cor_2 = "green";
											}elseif($tot_2<100 && $tot_2>=80){
												$cor_2 = "#FFB90F";
											}elseif($tot_2>=110){
												$cor_2 = "#1C86EE";
											}else{
												$cor_2 = "red";
											}
											$html_2 .= "<td align='center' class='cls_vals cls_real' onclick='send_form(\"" . implode(',',$codig_lnc) ."\",\"" . $Wb['banco_name'] . "\",\"fat\");'><b>R$ ".number_format($tt_2,2,',','.')."</b></td>";
											$html_2 .= "<td align='center' class='cls_vals'><span class='box' style='background:$cor_2;'>&nbsp;</span>".$tot_2."%</td>";
										$codig_lnc="";
										}else{
											$html_2 .= "<td align='center' class='cls_vals'><span class='box' style='background:$bol;'>&nbsp;</span>".number_format(($tt_meta!=0?($Wbanc_qtd/($tt_meta)):0)*100,0,',','')."%</td>";
											//$html_2 .= "<td align='center' class='cls_vals'>$n--$m-</td>";
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$sum_mt_2 += $Wmeta_2['meta_valor'];
		$col_2=0;
		$html_2 .= "</tr>";
		if($Wrow_2==$n2){
			$html_2 .= "<tr>";
			$html_2 .= "<td><b>TOTAIS</b></td>";
			$html_2 .= "<td></td>";
			$html_2 .= "<td></td>";
			$html_2 .= "<td></td>";
			$html_2 .= "<td></td>";
			$html_2 .= "<td></td>";
			$html_2 .= "<td></td>";
			$html_2 .= "<td></td>";
			$html_2 .= "<td></td>";
			$html_2 .= "<td></td>";
			$html_2 .= "<td></td>";
			$html_2 .= "<td></td>";
			$html_2 .= "<td></td>";
			$html_2 .= "<td></td>";
			$html_2 .= "<td></td>";
			$html_2 .= "</tr>";
		}
	}
	$html_2 .= "<input type='hidden' name='codig_lnc' id='codig_lnc' />";
	$html_2 .= "<input type='hidden' name='banco_lnc' id='banco_lnc' />";
	echo $html_2;
	?>
	</table>
	<br>
	<table align="center" height="6%" width="25%" border="1" cellspacing='3' cellpadding='3' id="tb_tot" style="font-family:arial;font-size:8pt; border-collapse: collapse;">
	<?php 
		$tot_3 = number_format(($sum_tt_2/$sum_mt_2)*100,1,',','');
		if($tot_3>=100 && $tot_3<110){
			$cor_3 = "green";
		}elseif($tot_3<100 && $tot_3>=80){
			$cor_3 = "#FFB90F";
		}elseif($tot_3>=110){
			$cor_3 = "#1C86EE";
		}else{
			$cor_3 = "red";
		}
		$html_3 .= "<tr>";
		$html_3 .= "<td align='center' style='$backg'>R$ ";
		$html_3 .= number_format($sum_mt_2,2,',','.') . "<br>";
		$html_3 .= "</td>";
		$html_3 .= "<td align='center' style='background:#ffffff'>R$ ";
		$html_3 .= number_format($sum_tt_2,2,',','.') . "<br>";
		$html_3 .= "</td>";
		$html_3 .= "<td align='center' style='background:#ffffff'><span class='box' style='background:$cor_3;'>&nbsp;</span>";
		$html_3 .= $tot_3 . "%";
		$html_3 .= "</td>";
	$html_3 .= "</tr>";
	echo $html_3;

	?>
	</table>
	<br>
	<br>
	<br>
	<br>
<?php 
}
//content-box
?>
<script>
	var altura = 0;
	altura = parseInt($("#tb_pro").height())+ parseInt($("#tb_fim").height())+parseInt($("#tb_tot").height())+200;
	//alert(altura);
	$("#content-box").css("height",altura);
</script>