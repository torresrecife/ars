<html height="100%">
<head>
</head>
<body>
<?php 

include('../php2/functions.php');
include('camp_config.php');

$html_camp = "";

$my_event = $_POST['event'];
$my_banco = $_POST['banco'];

$querys  = "";

$querys .= "SELECT ";

if($my_event=='Faturamento'){
	
	$querys .= " p.CodigoProcesso as 'Codigo', ";
	$querys .= " p.Comarca as 'comarca',";
	$querys .= " p.UFComarca as 'estado', ";
	$querys .= " (select top 1 pp.Pessoa from v_Parte_Processo as pp WITH (NOLOCK) where pp.TipoPessoa='Réu' and pp.CodigoProcesso=p.CodigoProcesso) as 'Adverso', ";
	$querys .= " p.NumeroProcessoCNJ as 'Processo', ";
	$querys .= " l.TipoLancamento as 'Andamento', ";
	$querys .= " l.valor as 'valores', ";
	$querys .= " FORMAT(l.DataHora_Evento, 'dd/MM/yyyy', 'en-US') as 'Data Evento', ";
	$querys .= " FORMAT(l.DataHora_Evento, 'dd/MM/yyyy', 'en-US')  as 'Data Cadastro' ";
	
	$querys .= " FROM v_Processo AS p WITH (NOLOCK)";
	$querys .= " JOIN v_Lancamento_Processo AS l WITH (NOLOCK) ON l.CodigoProcesso=p.CodigoProcesso ";
	$querys .= " where p.Carteira IN ( " . $bancos[$my_banco] . " ) ";  
	//$querys .= " AND l.ClassificaoLancamento = '".Honorário' ";
	//$querys .= " AND p.Area ='ATIVAS' ";
	//$querys .= " AND p.TipoJustica='Comum' ";
	$querys .= " AND l.StatusLancamento IN ('Pago pela Assessoria','Pendente na Assessoria','Enviado ao Contratante','Aprovado pelo Contratante','Pago pelo Contratante') ";
	//$querys .= " AND (p.Funcionario IN ('".utf8_decode('JOYCE NANCY RIOS JUSTINIANO DOS REIS')."','".utf8_decode('BRUNA ROBERTA NASCIMENTO RIOS')."','".utf8_decode('RANNY BRITO DOS SANTOS')."') OR p.Funcionario IS NULL) ";
	$querys .= " AND (p.NumeroContratoNeoCobranca=(SELECT top 1 pp.NumeroContratoNeoCobranca FROM v_Processo AS pp WITH (NOLOCK) WHERE pp.CodigoProcesso=l.CodigoProcesso ORDER BY pp.NumeroContratoNeoCobranca ASC ) OR p.NumeroContratoNeoCobranca IS NULL) ";
	$querys .= " AND MONTH(l.DataHora_Evento)=$mes ";
	$querys .= " AND YEAR(l.DataHora_Evento)=$ano ";
	if($day_ini!="" && $day_fim !=""){
		$querys .= " AND day(l.DataHora_Evento)>=$day_ini AND day(l.DataHora_Evento)<=$day_fim ";
	}
	
}else{
	$querys .= " a.CodigoProcesso as 'Codigo', ";
	$querys .= " p.Comarca as 'comarca',";
	$querys .= " p.UFComarca as 'estado', ";
	$querys .= " (select top 1 pp.Pessoa from v_Parte_Processo as pp WITH (NOLOCK) where pp.TipoPessoa='Réu' and pp.CodigoProcesso=a.CodigoProcesso) as 'Adverso', ";
	$querys .= " p.NumeroProcessoCNJ as 'Processo', ";
	$querys .= " a.TipoAndamentoProcesso as 'Andamento', ";
	$querys .= " FORMAT(a.DataHoraEvento, 'dd/MM/yyyy', 'en-US') as 'Data Evento', ";
	$querys .= " FORMAT(a.DataHora, 'dd/MM/yyyy', 'en-US')  as 'Data Cadastro' ";
	$querys .= " FROM v_Andamento_Processo AS a WITH (NOLOCK) ";
	$querys .= " JOIN v_Processo AS p ON p.CodigoProcesso=a.CodigoProcesso ";
	$querys .= " WHERE a.TipoAndamentoProcesso in (" . $andamentos[$my_event] . ") ";
	$querys .= " AND p.Carteira IN ( " . $bancos[$my_banco] . ") "; 
	$querys .= " AND MONTH(a.DataHoraEvento)=$mes ";
	$querys .= " AND YEAR(a.DataHoraEvento)=$ano ";
	if($day_ini!="" && $day_fim !=""){
		$querys .= " AND day(a.DataHoraEvento)>=$day_ini AND day(a.DataHoraEvento)<=$day_fim ";
	}
	$querys .= " AND p.TipoDesdobramento IS NULL ";
	$querys .= " AND a.Invalido='False' ";
}
//echo $querys;

$html_camp .= " <script type='text/javascript' src='../js/jquery-1.8.3.min.js'></script>";
$html_camp .= " <script type='text/javascript' src='../js/jFilterXCel2003.js'></script>";
$html_camp .= " <script type='text/javascript' src='../js/functions.js'></script>";
$html_camp .= " <link rel='stylesheet' href='../css/style.css'>";
$html_camp .= "<table align='center' width='80%' border='1' cellspacing='2' cellpadding='2' style='border-collapse:collapse;font-size:12pt;color:blue;font-family:arial' >";
$html_camp .= "<tr bgcolor='#ebebeb' >";
$html_camp .= "<td align='center' colspan='9' ><b>MÊS: ".strtoupper(data_br($mes,'emes'))." / ".$ano."</b><br>(" . ('Período entre '.$ars_ini.'/'.$mes.'/'.$ano.' e '.$ars_fim.'/'.$mes.'/'.$ano.'') . ")</td>";
$html_camp .= "</tr>";
$html_camp .= "<tr bgcolor='#ebebeb' >";
$html_camp .= "<td align='center'><b>N.</b></td>";
$html_camp .= "<td align='center'><b>Código</b></td>";
$html_camp .= "<td align='center'><b>Adverso</b></td>";
$html_camp .= "<td align='center'><b>Processo</b></td>";
$html_camp .= "<td align='center'><b>Comarca</b></td>";
$html_camp .= "<td align='center'><b>PE</b></td>";
$html_camp .= "<td align='center'><b>Andamento</b></td>";
if($my_event=='Faturamento'){
	$html_camp .= "<td align='center'><b>Valor</b></td>";
}
$html_camp .= "<td align='center'><b>D.Evento</b></td>";
$html_camp .= "<td align='center'><b>D.Cadastro</b></td>";
$html_camp .= "</tr>";

$n=1;
$a=0;
$vtotal=0;
$qr = sqlsrv_query($conexao1,$querys);
while($wr = sqlsrv_fetch_array($qr, SQLSRV_FETCH_ASSOC)){
	$a++;
	$estado		= converte_uf($wr['estado']);
	$comarca 	= htmlentities(remove_uf($wr['comarca']));
	$pasta 		= $wr['Codigo'];
	$processo	=$wr['Processo'];
	include('../php2/mylinktj.php');
	$html_camp .= "<tr>";
	$html_camp .= "<td align='center'>" . $n++ . "</td>";
	$html_camp .= "<td align='center'>" . $wr['Codigo'] . "</td>";
	$html_camp .= "<td align='center'>" . $wr['Adverso'] . "</td>";
	$html_camp .= "<td align='center'>" . $linktj . "</td>";
	$html_camp .= "<td align='center'>" . $comarca . "</td>";
	$html_camp .= "<td align='center'>" . $estado . "</td>";
	$html_camp .= "<td align='center'>" . $wr['Andamento'] . "</td>";
	if($my_event=='Faturamento'){
		$html_camp .= "<td align='right'> " . number_format($wr['valores'],2,",",".") . " </td>";
		$vtotal += $wr['valores']; 
	}
	$html_camp .= "<td align='right'> " . $wr['Data Evento'] . " </td>";
	$html_camp .= "<td align='right'> " . $wr['Data Cadastro'] . " </td>";
	$html_camp .= "</tr>";
}

$html_camp .= "</table>"; 
$html_camp .= "<script>"; 
$html_camp .= "$('button').css('font-size','12pt')"; 
$html_camp .= "</script>"; 
echo $html_camp;
echo "<table align='center' width='80%' border='0' cellspacing='2' cellpadding='2' style='border-collapse:collapse;font-size:12pt;color:blue;font-family:arial; font-weight:bold' >";
echo "<td align='left'>Banco 	  " . $my_banco ."</td>";
echo "<td align='right'>Total: R$ " . number_format($vtotal,2,",",".")."</td>";
echo "<td align='right'>Andamento: " . $my_event."</td>";
echo "</table>"; 
?>
</body>
</html>