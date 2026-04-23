<meta http-equiv='content-type' content='text/html; charset=utf-8'>
<?php 

include('../php2/functions.php');
include('camp_dados.php');

$mes = $_GET['mes']?$_GET['mes']:date('m');
$ano = $_GET['ano']?$_GET['ano']:date('Y');

if($_POST['banco']==""){
	echo "<br><br><br><br><br><br>";
	echo "<div style='font-size:18px;height:50px;text-align:center'>Volte e selecione o Banco!</div>";
	echo "<div style='font-size:18px;height:50px;text-align:center'><input type='button' onclick='javascript:window.history.back()' value='Voltar' style='cursor:pointer;height:30px;width:100px'/></div>";
	exit;
}

$banco = $_POST['banco'];

$ini_1 = $_POST['pri_ini'];
$ini_2 = $_POST['seg_ini'];
$ini_3 = $_POST['ter_ini'];
$ini_4 = $_POST['qua_ini'];

$fim_1 = $_POST['pri_fim'];
$fim_2 = $_POST['seg_fim'];
$fim_3 = $_POST['ter_fim'];
$fim_4 = $_POST['qua_fim'];

$mes = $_POST['mes']?$_POST['mes']:date('m');
$ano = $_POST['ano']?$_POST['ano']:date('Y');


$Sb = "select b.banco_id from bancos as b where b.banco_cod='" . $banco . "' ";
$Qb = mysqli_query($conexao4,$Sb);
$Wb = mysqli_fetch_array($Qb);

$Ssem = "select * from semanas as s where s.mes='" . $mes . "' and s.ano='" . $ano . "' limit 1 ";
$Qsem = mysqli_query($conexao4,$Ssem);
$Wsem = mysqli_fetch_array($Qsem);
//$Rsem = ($Wsem['ini_5']==0?4:5);

echo "<br><div style='font-family:arial;margin-left:40px'>Banco: <b>$banco</b> | Mês: <b>$mes</b> | Ano: <b>$ano</b></div><br>";


?>
<style>
td{
	border-left-width: 1px;
}
.cls_dados{
	background: #ebebeb;
}
.cls_sema{
	background: #ccc;
	font-weight:bold;
}
.cls_colun{
	width:100%;
	background: #1C86EE;
}
.box{
	margin-left:10px;
	float:left;
	width:15px;
	height:15px;
	border:1px solid #ccc;
	border-radius: 15px;
}
.cls_colun_2{
	width:7%;
	background: #FFB90F;
}
.cls_vals{
	width:6%;
}
.cls_indic{
	width:15%;
}
</style>
<table align="center" height="40%" width="95%" border="1" cellspacing='0' cellpadding='0' style="font-family:arial;font-size:9pt; border-collapse: collapse;">
	<tr>
<?php 
	$col = 6;

		//////////////////////////////////
	$sel  = " SELECT * ";
	$sel .= " FROM andamentos AS a ";
	$sel .= " JOIN banco_andamentos AS b ON a.anda_id=b.anda_id ";
	$sel .= " WHERE b.banco_id=" . $Wb['banco_id'] . " ";
	$sel .= " AND a.especie=1 ";
	$sel .= " AND b.stt='Y' ";
	$sel .= " ORDER BY a.especie asc ";
	$Qsel = mysqli_query($conexao4,$sel);
	$Wrow = mysqli_num_rows($Qsel);
	
	$Qmeta = mysqli_query($conexao4,"SELECT * FROM metas AS m WHERE m.banco_id=".$Wb['banco_id']." AND m.mes=" . $mes . " AND m.ano=" . $ano . " ");
	echo "SELECT * FROM metas AS m WHERE m.banco_id=".$Wb['banco_id']." AND m.mes=" . $mes . " AND m.ano=" . $ano . "";
	//$Wmeta = mysqli_fetch_array($Qmeta);
	while($Wmeta = mysqli_fetch_array($Qmeta)){	
		$dados[$Wmeta['banco_id']] = array($Wmeta[1]);
	}
	
	echo "<pre>";
	print_r($dados);
	echo "</pre>";
	
	for($i=1;$i<=$col;$i++){
		
		switch($i){
			case 1:
				echo "<td valign='top' width='1%' >";
				echo "<table align='center' height='100%' width='100%' border='1' cellspacing='2' cellpadding='2' style='font-family:arial;font-size:9pt; border-collapse: collapse;'>";
						echo "<tr height='10%'>";
							echo "<td>&nbsp;</td>";
						echo "</tr>";
						echo "<tr height='90%'>";
							echo "<td align='center' class='cls_colun'><div style='color:#FFF;transform: rotate(270deg);'><b>OPERACAO</b></div></td>";
						echo "</tr>";
					echo "</table>";
				echo "</td>";
				break;
			case 2:
				echo "<td valign='top' width='20%' >";
					echo "<table align='center' height='100%' width='100%' border='1' cellspacing='2' cellpadding='2' style='font-family:arial;font-size:9pt; border-collapse: collapse;'>";
						$n=0;
						$num=1;
						echo "<tr>";
								echo "<td align='center' class='cls_sema cls_indic'>INDICADOR</td>";
						echo "</tr>";
						while($Wsel = mysqli_fetch_array($Qsel)){	
							echo "<tr>";
								echo "<td>". $Wsel['nome']."</td>";
							echo "</tr>";
						}
					echo "</table>";
				echo "</td>";
				break;
			case 2:
				echo "<td valign='top' width='20%' >";
					echo "<table align='center' height='100%' width='100%' border='1' cellspacing='2' cellpadding='2' style='font-family:arial;font-size:9pt; border-collapse: collapse;'>";
						$n=0;
						$num=1;
						echo "<tr>";
								echo "<td align='center' class='cls_sema cls_indic'>INDICADOR</td>";
						echo "</tr>";
						while($Wsel = mysqli_fetch_array($Qsel)){	
							echo "<tr>";
								echo "<td>".$Wsel['nome']."</td>";
							echo "</tr>";
						}
					echo "</table>";
				echo "</td>";
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
?>
	</tr>
</table>
<?php
exit;
?>
	<tr>
		<td align="center" rowspan="2" ></td>
		<td align="center" rowspan="2" class="cls_sema cls_indic">INDICADOR</td>
		<?php 
		if($Wsem['ini_1'] != "" && $Wsem['fim_1'] !=""){
			?>
			<td align="center" colspan="3" class="cls_sema"><?php echo $Wsem['ini_1']; ?> à <?php echo $Wsem['fim_1']; ?></td>
			<td align="center" colspan="3" class="cls_sema"><?php echo $Wsem['ini_2']; ?> à <?php echo $Wsem['fim_2']; ?></td>
			<td align="center" colspan="3" class="cls_sema"><?php echo $Wsem['ini_3']; ?> à <?php echo $Wsem['fim_3']; ?></td>
			<td align="center" colspan="3" class="cls_sema"><?php echo $Wsem['ini_4']; ?> à <?php echo $Wsem['fim_4']; ?></td>
			<td align="center" rowspan="2" class="cls_sema cls_vals">TOTAL		</td>
			<?php 
		}else{
		?>
			<td align="center" colspan="3" class="cls_sema"><?php echo date('d', strtotime(P_semana($mes,$ano,1,"ini"))); ?> a <?php echo date('d', strtotime(P_semana($mes,$ano,1,"fim"))); ?></td>
			<td align="center" colspan="3" class="cls_sema"><?php echo date('d', strtotime(P_semana($mes,$ano,2,"ini"))); ?> a <?php echo date('d', strtotime(P_semana($mes,$ano,2,"fim"))); ?></td>
			<td align="center" colspan="3" class="cls_sema"><?php echo date('d', strtotime(P_semana($mes,$ano,3,"ini"))); ?> a <?php echo date('d', strtotime(P_semana($mes,$ano,3,"fim"))); ?></td>
			<td align="center" colspan="3" class="cls_sema"><?php echo date('d', strtotime(P_semana($mes,$ano,4,"ini"))); ?> a <?php echo date('d', strtotime(P_semana($mes,$ano,4,"fim"))); ?></td>
			<td align="center" rowspan="2" class="cls_sema cls_vals">TOTAL		</td>
			<?php 
		}
		?>
	</tr>
	<tr>
		<td align="center" class="cls_dados cls_vals">META		</td>
		<td align="center" class="cls_dados cls_vals">REALIZADO	</td>
		<td align="center" class="cls_dados cls_vals">FAROL		</td>
		<td align="center" class="cls_dados cls_vals">META		</td>
		<td align="center" class="cls_dados cls_vals">REALIZADO	</td>
		<td align="center" class="cls_dados cls_vals">FAROL		</td>
		<td align="center" class="cls_dados cls_vals">META		</td>
		<td align="center" class="cls_dados cls_vals">REALIZADO	</td>
		<td align="center" class="cls_dados cls_vals">FAROL		</td>
		<td align="center" class="cls_dados cls_vals">META		</td>
		<td align="center" class="cls_dados cls_vals">REALIZADO	</td>
		<td align="center" class="cls_dados cls_vals">FAROL		</td>
	</tr>
<?php 
$n=0;
//////////////////////////////////
$sel  = " SELECT * ";
$sel .= " FROM andamentos AS a ";
$sel .= " JOIN banco_andamentos AS b ON a.anda_id=b.anda_id ";
$sel .= " WHERE b.banco_id=" . $Wb['banco_id'] . " ";
$sel .= " AND a.especie=1 ";
$sel .= " AND b.stt='Y' ";
$sel .= " ORDER BY a.especie asc ";
$Qsel = mysqli_query($conexao4,$sel);
$Wrow = mysqli_num_rows($Qsel);

$num=1;
while($Wsel = mysqli_fetch_array($Qsel)){
	$n++;
	
	$Qmeta = mysqli_query($conexao4,"SELECT * FROM metas AS m WHERE m.banco_id=".$Wsel['banco_id']." AND m.mes=" . $mes . " AND m.ano=" . $ano . " ");
	$Wmeta = mysqli_fetch_array($Qmeta);
	
	$Qct = mysqli_query($conexao4,"SELECT c.carteira_vinc FROM carteira AS c WHERE c.banco_id=".$Wsel['banco_id']." AND c.carteira_condicao='Carteira' ");
	$Wct = mysqli_fetch_array($Qct);
	$carteira_vinc = $Wct['carteira_vinc'];
	
	$condicao="";
	$Qcart = mysqli_query($conexao4,"SELECT d.dados_cod,c.carteira_vinc,c.carteira_cod FROM dados AS d JOIN carteira AS c ON c.banco_id=d.dados_id WHERE d.banco_id=".$Wsel['banco_id']." ");
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
	
	echo "<tr>";
	
	for($m=1;$m<=15;$m++){
		if($n==1 && $m==1){
			echo "<td align='center' rowspan='".$Wrow."' class='cls_colun'><div style='color:#FFF;transform: rotate(270deg);'><b>OPERACAO</b></div></td>";
		}else{
			if($n==1 && $m==2){
				echo "<td class='cls_indic'>".$Wsel['nome']."</td>";
					
			}else{
				if(($n==2 || $n==3 || $n==4 || $n==5 || $n==6) && $m==1){
					echo "<td class='cls_indic'>".$Wsel['nome']."</td>";
				}else{
					if($n>1 && ($m==2 || $m==5 || $m==8 || $m==11)){
						echo "<td align='center' class='cls_vals'>".($Wmeta[$Wsel['chave']]/4)."</td>";
					}else{
						if($n==1 && ($m==3 || $m==6 || $m==9 || $m==12)){
							echo "<td align='center' class='cls_vals'>".($Wmeta[$Wsel['chave']]/4)."</td>";
						}else{
							if($m==14 && $n>1){
								//echo "<td align='center'>".$Wbanc['qtd']." -".$n."</td>";
							}else{
								if(($n==1 && ($m==4 || $m==7 || $m==10 || $m==13)) || ($n>1 && ($m==3 || $m==6 || $m==9 || $m==12)) ){
									/////////pegando os valores depositados
									$c++;
									$qntd  = " SELECT sum(1) as 'qtd' FROM Andamento_Processo AS a ";
									$qntd .= " JOIN Processo AS p ON p.CodigoProcesso=a.CodigoProcesso ";
									$qntd .= " WHERE a.TipoAndamentoProcesso IN (".$andamentos[$Wsel['anda_neo']].") "; 
									$qntd .= " AND p.TipoProcesso NOT IN ('CARTA PRECATÓRIA') ";
									if($carteira_vinc=="LIKE"){
										$qntd .= " AND p.Carteira LIKE '%".str_replace("'","",$condicao)."%' ";
									}else{
										$qntd .= " AND p.Carteira IN (".$condicao.") ";
									}
									if( ${"ini_" . $c} != "" && ${"fim_" . $c} !=""){
										$qntd .= " AND (day(a.DataHoraEvento)>=" . ${'ini_' . $c} . " AND day(a.DataHoraEvento)<=" . ${'fim_' . $c} . ")";
									}else{
										$qntd .= " AND (day(a.DataHoraEvento)>=" . date('d', strtotime(P_semana($mes,$ano,$c,"ini"))) . " AND day(a.DataHoraEvento)<=" . date('d', strtotime(P_semana($mes,$ano,$c,"fim"))) . ")";
									}
									$qntd .= " AND MONTH(a.DataHoraEvento)=$mes ";
									$qntd .= " AND YEAR(a.DataHoraEvento)=$ano ";
									$qntd .= " AND p.TipoDesdobramento IS NULL AND a.Invalido='False' ";
									//echo $qntd . "<br>";
									$Qqntd = sqlsrv_query($conexao1,$qntd);
									$Wbanc = sqlsrv_fetch_array($Qqntd, SQLSRV_FETCH_ASSOC);
									echo "<td align='center' class='cls_vals'>".$Wbanc['qtd']."</td>";
									$tt += $Wbanc['qtd'];
								}else{
									$resul = number_format(($Wbanc['qtd']/($Wmeta[$Wsel['chave']]/4))*100,0,',','');
									if($resul>=100 && $resul<110){
										$bol = "green";
									}elseif($resul<100 && $resul>=80){
										$bol = "#FFB90F";
									}elseif($resul>=110){
										$bol = "#1C86EE";
									}else{
										$bol = "red";
									}
									if($m==15){
										echo "<td align='center' class='cls_vals'>".$tt."</td>";
									}else{
										echo "<td align='center' class='cls_vals'><span class='box' style='background:$bol;'>&nbsp;</span>".number_format(($Wbanc['qtd']/($Wmeta[$Wsel['chave']]/4))*100,0,',','')."%</td>";
									}
								}
							}
						}
					}
				}
			}
		}
	}
	$tt=0;
	echo "</tr>";
}

?>
</table>
<br>
<br>
<table align="center" height="20%" width="95%" border="1" cellspacing='3' cellpadding='3' style="font-family:arial;font-size:9pt; border-collapse: collapse;">
<?php 
$sel_2  = " SELECT * ";
$sel_2 .= " FROM andamentos AS a ";
$sel_2 .= " JOIN banco_andamentos AS b ON a.anda_id=b.anda_id ";
$sel_2 .= " WHERE b.banco_id=" . $Wb['banco_id'] . " ";
$sel_2 .= " AND a.especie=2 ";
$sel_2 .= " AND b.stt='Y' ";
$sel_2 .= " ORDER BY a.especie asc ";
$Qsel_2 = mysqli_query($conexao4,$sel_2);
$Wrow_2 = mysqli_num_rows($Qsel_2);
$n2=0;
$num_2=1;
while($Wsel_2 = mysqli_fetch_array($Qsel_2)){
	$n2++;
	
	$Qmeta_2 = mysqli_query($conexao4,"SELECT * FROM metas AS m WHERE m.banco_id=".$Wsel_2['banco_id']." AND m.mes=" . $mes . " AND m.ano=" . $ano . " ");
	$Wmeta_2 = mysqli_fetch_array($Qmeta_2);
	
	$Qct_2 = mysqli_query($conexao4,"SELECT c.carteira_vinc FROM carteira AS c WHERE c.banco_id=".$Wsel_2['banco_id']." AND c.carteira_condicao='Carteira' ");
	$Wct_2 = mysqli_fetch_array($Qct_2);
	$carteira_vinc_2 = $Wct_2['carteira_vinc'];
	
	$condicao_2="";
	$Qcart_2 = mysqli_query($conexao4,"SELECT d.dados_cod,c.carteira_vinc,c.carteira_cod FROM dados AS d JOIN carteira AS c ON c.banco_id=d.dados_id WHERE d.banco_id=".$Wsel_2['banco_id']." ");
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
	
	echo "<tr>";
	
	for($m=1;$m<=15;$m++){
		if($n2==1 && $m==1){
			echo "<td align='center' rowspan='".$Wrow_2."' class='cls_colun_2'><div style='color:#FFF;transform: rotate(270deg);'><b>FINANCEIRO</b></div></td>";
		}else{
			if($n2==1 && $m==2){
				echo "<td class='cls_indic'>".$Wsel_2['nome']."</td>";
					
			}else{
				if(($n2==2 || $n2==3 || $n2==4 || $n2==5 || $n2==6) && $m==1){
					echo "<td class='cls_indic'>".$Wsel_2['nome']."</td>";
				}else{
					if($n2>1 && ($m==2 || $m==5 || $m==8 || $m==11)){
						echo "<td align='center' class='cls_vals'>R$ ".number_format(($Wmeta_2[$Wsel_2['chave']]/4),2,',','.')."</td>";
					}else{
						if($n2==1 && ($m==3 || $m==6 || $m==9 || $m==12)){
							echo "<td align='center' class='cls_vals'>R$ ". number_format(($Wmeta_2[$Wsel_2['chave']]/4),2,',','.') ."</td>";
						}else{
							if($m==14 && $n2>1){
								//echo "<td align='center'>".$Wbanc['qtd']." -".$n2."</td>";
							}else{
								if(($n2==1 && ($m==4 || $m==7 || $m==10 || $m==13)) || ($n2>1 && ($m==3 || $m==6 || $m==9 || $m==12)) ){
									/////////pegando os valores depositados
									$c2++;
									$qntd_2  = " SELECT sum(l.Valor) as 'qtd' ";
									$qntd_2 .= " FROM Processo AS p ";
									$qntd_2 .= " JOIN Lancamento_Processo AS l ON l.CodigoProcesso=p.CodigoProcesso ";
									$qntd_2 .= " WHERE l.TipoLancamento IN (".$Wsel_2['anda_neo'].") "; 
									if($carteira_vinc_2=="LIKE"){
										$qntd_2 .= " AND p.Carteira LIKE '%".str_replace("'","",$condicao_2)."%' ";
									}else{
										$qntd_2 .= " AND p.Carteira IN (".$condicao_2.") ";
									}
									if( ${"ini_" . $c2} != "" && ${"fim_" . $c2} !=""){
										$qntd_2 .= " AND (day(l.DataHora_Evento)>=" . ${'ini_' . $c2} . " AND day(l.DataHora_Evento)<=" . ${'fim_' . $c2} . ")";
									}else{
										$qntd_2 .= " AND (day(l.DataHora_Evento)>=" . date('d', strtotime(P_semana($mes,$ano,$c2,"ini"))) . " AND day(l.DataHora_Evento)<=" . date('d', strtotime(P_semana($mes,$ano,$c2,"fim"))) . ")";
									}
									$qntd_2 .= " AND MONTH(l.DataHora_Evento)=$mes ";
									$qntd_2 .= " AND YEAR(l.DataHora_Evento)=$ano ";
									
									//echo $qntd_2 . "<br>";
									
									$Qqntd_2 = sqlsrv_query($conexao1,$qntd_2);
									$Wbanc_2 = sqlsrv_fetch_array($Qqntd_2, SQLSRV_FETCH_ASSOC);
									echo "<td align='center' class='cls_vals'>R$ ".number_format(($Wbanc_2['qtd']?$Wbanc_2['qtd']:0),2,',','.')."</td>";
									$tt_2 += $Wbanc_2['qtd'];
								}else{
									$resul = number_format(($Wbanc_2['qtd']/($Wmeta_2[$Wsel_2['chave']]/4))*100,0,',','');
									if($resul>=100 && $resul<110){
										$bol = "green";
									}elseif($resul<100 && $resul>=80){
										$bol = "#FFB90F";
									}elseif($resul>=110){
										$bol = "#1C86EE";
									}else{
										$bol = "red";
									}
									if($m==15){
										echo "<td align='center' class='cls_vals'>R$ ".number_format($tt_2,2,',','.')."</td>";
									}else{
										echo "<td align='center' class='cls_vals'><span class='box' style='background:$bol;'>&nbsp;</span>".number_format(($Wbanc_2['qtd']/($Wmeta_2[$Wsel_2['chave']]/4))*100,0,',','')."%</td>";
										//echo "<td align='center' class='cls_vals'>$n--$m-</td>";
									}
								}
							}
						}
					}
				}
			}
		}
	}
	$tt_2=0;
	echo "</tr>";
}
?>
</table>