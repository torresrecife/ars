<meta http-equiv='content-type' content='text/html; charset=utf-8'>
	
<?php 

protegePagina(0);

$banco = $_POST['hid_flag'];
$startDate = $_POST['startDate']?$_POST['startDate']:date('M');
$startSetor = $_POST['startSetor']?$_POST['startSetor']:"";
$mes = $_POST['mes']?$_POST['mes']:date('m');
$ano = $_POST['ano']?$_POST['ano']:date('Y');
$tds = 0;

$Sb = "select b.banco_id,b.banco_cod from bancos as b where b.banco_id='" . $banco . "' ";
$Qb = mysqli_query($conexao4,$Sb);
$Wb = mysqli_fetch_array($Qb);

$Ssem = "select * from semanas as s where s.mes='" . $mes . "' and s.ano='" . $ano . "' limit 1 ";
$Qsem = mysqli_query($conexao4,$Ssem);
$Wsem = mysqli_fetch_array($Qsem);

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

$uteis_atu = diasUteis($data_ini,$data_fim);
$uteis_mes = diasUteis($data_ini,$data_fms);

/////contagem dos dias úteis////
$n=0;
//////////////////////////////////

$sel  = " SELECT * ";
$sel .= " FROM bancos AS b ";
$sel .= " where 1=1 ";
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

echo "<button name='geral' value='0' style='float:right;margin-right:50px;border:1px dotted #999;'>Semanal</button>";
echo "<br><div style='font-family:arial;margin-left:40px;font-size:10pt;'>$tit_are | Mês / Ano: <b>$startDate</b> </div><br>";

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
	border-botton:1px dotted #999;
	border-left:1px dotted #999;
	border-right:1px dotted #999;
	height:30px;
}
td.cls_body{
	border-left-width: 1px;
	border:1px dotted #999;
	height:30px;
}

.cls_dados{
	background:#DBDBDB;
}
.cls_sema{
	background: #ccc;
	font-weight:bold;
}
.cls_colun{
	width:4%;
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
	height:30px;
	width:4%;
	background: #FFB90F;
}
.cls_vals{
	width:6%;
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
</style>
<table align="center" height="50%" width="60%" border="0" cellspacing='3' cellpadding='3' style="font-family:arial;font-size:8pt; border-collapse: collapse;background:#ffffff">
	<tr>
		<td align="center" colspan="6" class="cls_indic">PRODUÇÃO - BVAA</td>
		<td align="center" colspan="0" ></td>
		<td align="center" colspan="1" class="cls_indic2">TOTAL</td>
	</tr>
	<tr>
		<td align="center" class="cls_dados cls_vals" >CLIENTE	</td>
		<td align="center" class="cls_dados cls_vals" >META/MÊS</td>
		<td align="center" class="cls_dados cls_vals" >META/HOJE</td>
		<td align="center" class="cls_dados cls_vals" >REALIZADO</td>
		<td align="center" class="cls_dados cls_vals" >SALDO (Parcial)	</td>
		<td align="center" class="cls_dados cls_perc" >PERC / HOJE</td>
		<td align="center" class="cls_perc2" >&nbsp;</td>
		<td align="center" class="cls_dados cls_perc" >PERC / MÊS </td>
	</tr>
<?php

while($Wsel = mysqli_fetch_array($Qsel)){
	$lin++;
	$html_1 .= "<tr style='height:30px'>";
	$html_1 .= "<td class='cls_body' style='padding-left:5px'>" . $Wsel['banco_name'] . "</td>";
	$Qand = mysqli_query($conexao4,"SELECT a.anda_id, m.meta_valor, anda_neo FROM metas_andamentos AS m JOIN andamentos AS a ON m.anda_id=a.anda_id WHERE m.banco_id='".$Wsel['banco_id']."' AND m.meta_mes=" . $mes . " AND m.meta_ano=" . $ano . " AND a.especie=2 GROUP BY a.anda_id, m.meta_valor ");
	$lancamentos="";
	while($Wand = mysqli_fetch_array($Qand)){
		$mt_mes += $Wand['meta_valor'];
		if(($ln++)>0){
			$lancamentos .= ",";
		}
		$lancamentos .= $Wand['anda_neo'];
	}
	$vl_unt = ($mt_mes/$uteis_mes);
	$html_1 .= "<td class='cls_body' align='center'>R$ " . number_format($mt_mes,2,",",".") . "</td>";
	
		////////
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
		$Wbanc_qtd = 0;
		$codig_lnc = array();
		if($lancamentos!=""){
			////////honorários neo jurídico//////////////////
			$querys .= " SELECT l.CodigoLancamento, l.Valor ";
			$querys .= " FROM Processo AS p WITH (NOLOCK)";
			$querys .= " JOIN Lancamento_Processo AS l WITH (NOLOCK) ON l.CodigoProcesso=p.CodigoProcesso ";
			$querys .= " AND p.Carteira IN ( " . $condicao . " ) ";  
			$querys .= " AND l.TipoLancamento IN ('".(str_replace(",","','",$lancamentos))."') ";  
			//$querys .= " AND l.ClassificaoLancamento = '".('Honorário')."' ";
			$querys .= " AND l.StatusLancamento IN ('Pago pela Assessoria','Pendente na Assessoria','Enviado ao Contratante','Aprovado pelo Contratante','Pago pelo Contratante') ";
			//$querys .= " AND p.Funcionario IS NULL ";
			$querys .= " AND MONTH(l.DataHora_Evento)=$mes ";
			$querys .= " AND YEAR(l.DataHora_Evento)=$ano ";
			$querys .= " GROUP BY l.CodigoLancamento, l.Valor ";
			//echo $querys . "<hr>";
			$Qqntd = sqlsrv_query($conexao1,$querys) or die(print_r(sqlsrv_errors()));
			while($Wbanc_2 = sqlsrv_fetch_array($Qqntd, SQLSRV_FETCH_ASSOC)){
				$Wbanc_qtd += $Wbanc_2['Valor'];
				$codig_lnc[] = $Wbanc_2['CodigoLancamento'];
			}
		}
		
	$valor_3 = $Wbanc_qtd;
	$m_hoje = ($uteis_atu!=0?($vl_unt*$uteis_atu):0);
	$valor_4 = number_format(( ($mt_mes!=0?($valor_3/$mt_mes):0) *100),1,",",".");			
	$hcor = number_format((($m_hoje!=0?($valor_3/$m_hoje):0) *100),1,",",".");		
	$cor =  $hcor==0?"#F0F0F0":($hcor<80?"red":($hcor>79 && $hcor<100?"#FFB90F":($hcor>=100 && $hcor<110?"green":($hcor>109?"#1C86EE":""))));
	
	$html_1 .= "<td class='cls_body' align='center'>R$ " . number_format($m_hoje,2,",",".") . "</td>";
	$html_1 .= "<td class='cls_body cls_real' align='center' onclick='send_form(\"" . implode(',',$codig_lnc)."\",\"" . $Wsel['banco_name'] . "\");'>R$ " . number_format($valor_3,2,",",".") . "</td>";
	
	$html_1 .= "<td class='cls_body' align='center'>R$ " . number_format(($valor_3-$m_hoje),2,",",".") . "</td>";
	$html_1 .= "<td class='cls_body' align='center' style='background:$cor;color:#000'>" . $hcor . "%</td>";
	$html_1 .= "<td class='' align='center' style='background:#fff;color:#000;border-botton:0;border-top:0;'>&nbsp;</td>";
	$html_1 .= "<td class='cls_body' align='center' style='background:$cor;color:#000'><b>" . $valor_4 . "%</b></td>";
	$html_1 .= "</tr>";
	
	$tt_mt_mes += $mt_mes;
	$tt_m_hoje += $m_hoje;
	$tt_realiz += $valor_3;
	
	$mt_mes = 0;
	$m_hoje = 0;
	$querys="";
}
	$html_1 .= "<input type='hidden' name='codig_lnc' id='codig_lnc' />";
	$html_1 .= "<input type='hidden' name='banco_lnc' id='banco_lnc' />";
	$html_1 .= "<input type='hidden' name='startSetor' value='$startSetor'/>";
	$html_1 .= "<input type='hidden' name='startDate'  value='$startDate' />";
	$html_1 .= "<input type='hidden' name='mes'  value='$mes' />";
	$html_1 .= "<input type='hidden' name='ano'  value='$ano' />";
	
echo $html_1;

$tt_perc_mes = ($tt_realiz/$tt_mt_mes)*100;
$tt_perc_hoj = ($tt_realiz/$tt_m_hoje)*100;

$tt_cor =  $tt_perc_hoj==0?"#F0F0F0":($tt_perc_hoj<80?"red":($tt_perc_hoj>79 && $tt_perc_hoj<100?"#FFB90F":($tt_perc_hoj>=100 && $tt_perc_hoj<110?"green":($tt_perc_hoj>109?"#1C86EE":""))));

echo "<tr>";
echo "<td colspan='8'>";
echo "</td>";
echo "</tr>";
echo "<tr class='cls_dados'>";
echo "<td align='center' class='cls_body'><b>TOTAIS</b></td>";
echo "<td align='center' class='cls_body'><b>R$ " . number_format($tt_mt_mes,2,",",".") . "</b></td>";
echo "<td align='center' class='cls_body'><b>R$ " . number_format($tt_m_hoje,2,",",".") . "</b></td>";
echo "<td align='center' class='cls_body'><b>R$ " . number_format($tt_realiz,2,",",".") . "</b></td>";
echo "<td align='center' class='cls_body'><b>R$ " . number_format(($tt_realiz-$tt_m_hoje),2,",",".") . "</b></td>";
echo "<td align='center' class='cls_perc' style='background:$tt_cor'><b>" . number_format($tt_perc_hoj,1,",",".") 	. "%</b></td>";
echo "<td align='center' class='cls_perc2'>&nbsp;</td>";
echo "<td align='center' class='cls_perc' style='background:$tt_cor'><b>" . number_format($tt_perc_mes,1,",",".") 	. "%</b></td>";
echo "</tr>";

?>
</table>
<style>
#content-box{
	height:<?php echo ($lin*30)+360; ?>px;
}
</style>