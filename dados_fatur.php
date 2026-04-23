<html height="100%">
<script type='text/javascript' src='js/jquery-1.8.0.min.js'></script>
<script type='text/javascript' src='js/jFilterXCel2003.js'></script>
<script type='text/javascript' src='js/functions.js'></script>
<script type='text/javascript'>
$(document).ready(function(){
	carregarFiltros('tbf1');
	$("tr").dblclick(function(){
		$(this).css("background","#ffffff");
	});
	$("tr").click(function(){
		$(this).css("background","yellow");
	});
});
function enviar_neo(valor){
	window.open("http://192.168.81.200/Modulos/ElementosProcessuais/ProcessoFichaGeral.aspx?idProcesso="+valor);
}
</script>
<style>
.cls_real:hover{
	background:#ebebeb;
	cursor:pointer;
}
</style>
<body>
<?php

include('../php2/conexao3.php');
include('../php2/functions.php');

$codig_lnc = $_POST['codig_lnc'];
$banco_lnc = $_POST['banco_lnc'];

$querys  = "";
$querys .= "SELECT ";
$html_camp = "";

if($codig_lnc!=""){	
	$querys .= " p.CodigoProcesso as 'Codigo', ";
	$querys .= " p.Comarca as 'comarca',";
	$querys .= " p.UFComarca as 'estado', ";
	$querys .= " (select top 1 pp.Pessoa from v_Parte_Processo as pp WITH (NOLOCK) where pp.TipoPessoa='Réu' and pp.CodigoProcesso=p.CodigoProcesso) as 'Adverso', ";
	$querys .= " (select top 1 pp.Pessoa from v_Parte_Processo as pp WITH (NOLOCK) where pp.TipoPessoa='Autor' and pp.CodigoProcesso=p.CodigoProcesso) as 'Adverso2', ";
	$querys .= " p.Area, ";
	$querys .= " p.NumeroProcessoCNJ as 'Processo', ";
	$querys .= " p.ContaContratoNeoCobranca as 'ContaContratoNeoCobranca', ";
	$querys .= " l.TipoLancamento as 'Andamento', ";
	$querys .= " l.valor as 'valores', ";
	$querys .= " FORMAT(l.DataHora_Evento, 'dd/MM/yyyy', 'en-US') as 'Data Evento', ";
	$querys .= " FORMAT(l.DataHora_Evento, 'dd/MM/yyyy', 'en-US')  as 'Data Cadastro' ";
	
	$querys .= " FROM v_Processo AS p WITH (NOLOCK) ";
	$querys .= " JOIN v_Lancamento_Processo AS l ON l.CodigoProcesso=p.CodigoProcesso ";
	$querys .= " where l.CodigoLancamento in (".$codig_lnc.") ";
	$querys .= " ORDER BY l.DataHora_Evento ASC";
} 

$html_camp .= "<table align='center' width='70%' id='tbf1' border='1' cellspacing='5' cellpadding='5' bordercolor='#ccc' style='border-collapse:collapse;font-size:10pt;color:#333;font-family:arial;margin-top:20px' >";
$html_camp .= "<tr bgcolor='#ebebeb' >";
$html_camp .= "<th align='center' class='comFiltro'><b>N.</b></td>";
$html_camp .= "<th align='center' class='comFiltro'><b>Código</b></td>";
$html_camp .= "<th align='center' class='comFiltro'><b>Adverso</b></td>";
$html_camp .= "<th align='center' class='comFiltro'><b>Processo</b></td>";
$html_camp .= "<th align='center' class='comFiltro'><b>Conta</b></td>";
$html_camp .= "<th align='center' class='comFiltro'><b>Comarca</b></td>";
$html_camp .= "<th align='center' class='comFiltro'><b>UF</b></td>";
$html_camp .= "<th align='center' class='comFiltro'><b>Andamento</b></td>";
$html_camp .= "<th align='center' class='comFiltro'><b>Valor</b></td>";
$html_camp .= "<th align='center' class='comFiltro'><b>D.Evento</b></td>";
$html_camp .= "<th align='center' class='comFiltro'><b>D.Cadastro</b></td>";
$html_camp .= "</tr>";

$n=1;
$a=0;
$vtotal=0;

//print_r($querys);
//exit;


$qr = sqlsrv_query($conexao1,$querys);
while($wr = sqlsrv_fetch_array($qr, SQLSRV_FETCH_ASSOC)){
	//include('../php2/mylinktj.php');
	//$linktj
	
	$a++;
	$estado		= converte_uf($wr['estado']);
	$comarca 	= htmlentities(remove_uf($wr['comarca']));
	$pasta 		= $wr['Codigo'];
	$processo	= $wr['Processo'];
	$html_camp .= "<tr>";
	$html_camp .= "<td align='center' class='cls_td'>" . $n++ . "</td>";
	$html_camp .= "<td align='center' class='cls_real' onclick='enviar_neo(" . $wr['Codigo'] . ")'>" . $wr['Codigo'] . "</td>";
	if($wr['Area'] === 'PASSIVAS') {
		$html_camp .= "<td align='center'>" . $wr['Adverso2'] . "</td>";
	} else {
		$html_camp .= "<td align='center'>" . $wr['Adverso'] . "</td>";	
	}
	$html_camp .= "<td align='center'>" . ($processo==""?"-":$processo) . "</td>";
	$html_camp .= "<td align='center'>" . $wr['ContaContratoNeoCobranca']. "</td>";
	$html_camp .= "<td align='center'>" . $comarca. "</td>";
	$html_camp .= "<td align='center'>" . $estado . "</td>";
	$html_camp .= "<td align='center'>" . $wr['Andamento'] . "</td>";
	$html_camp .= "<td align='right' class='cls_rs'> " . number_format($wr['valores'],2,",",".") . " </td>";
	$vtotal += $wr['valores']; 
	$html_camp .= "<td align='right'> " . $wr['Data Evento'] . " </td>";
	$html_camp .= "<td align='right'> " . $wr['Data Cadastro'] . " </td>";
	$html_camp .= "</tr>";
}

$html_camp .= "</table>"; 
echo $html_camp;
echo "<table align='center' width='70%' border='0' cellspacing='2' cellpadding='2' style='border-collapse:collapse;font-size:10pt;color:#333;font-family:arial; font-weight:bold;margin-top:20px' >";
echo "<td align='left'>Banco: " . $banco_lnc ."</td>";
echo "<td align='left'><span class='titulo_r' id='id_sel' >Total Selecionado: $a</span></td>";
echo "<td align='right'><div id='id_crs' >Valor Total: <b>" . number_format($vtotal,2,',','.') . "</b></div></td>";
echo "<td align='right'>Lançamentos</td>";
echo "</table>"; 
echo "<br>"; 

$table=$html_camp;
include("../php2/exportar.php");

?>
</body>
</html>