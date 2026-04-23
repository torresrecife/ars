<html height="100%">
<head>
</head>
<body>
<?php 

$ars_mes=$_POST['ars_mes']?$_POST['ars_mes']:"";
$ars_ano=$_POST['ars_ano']?$_POST['ars_ano']:"";

$ars_ini=$_POST['ars_ini']?$_POST['ars_ini']:"";
$ars_fim=$_POST['ars_fim']?$_POST['ars_fim']:"";

include('../php2/functions.php');

include('camp_dados.php');



$html_camp = "";
$html_camp .= "
<script type='text/javascript' src='../js/jquery-1.9.1.js'></script>
<script> 

function enviar(valor){
	$('#ars_ini').val($('#'+valor+'_ini').val());
	$('#ars_fim').val($('#'+valor+'_fim').val());
	
	$('#enviar').submit();
}
</script>
<style>
td{
	border:1px solid #999;
}
</style>
";
$html_camp .= " <script type='text/javascript' src='../js/jquery-1.8.3.min.js'></script>";
$html_camp .= "<table align='left' width='20%' border='0' cellspacing='2' cellpadding='2' style='border-collapse:collapse;font-size:10pt;color:blue;font-family:arial; float:left; position:absolute;' >";
$html_camp .= "<form action='camp_resumo.php' method='post' id='enviar'>";
$html_camp .= "<tr><td>Mês/Ano:</td><td colspan='2'><input type='text' id='mes' name='ars_mes' style='width:60px' value='".$ars_mes."'></td><td><input type='text' id='ano' name='ars_ano' style='width:60px' value='".$ars_ano."'></td></tr>";
$html_camp .= "<tr><td>Primeira Semana: </td><td><input type='text' id='p_ini' name='p_ini' style='width:30px' value='".$_POST['p_ini']."'></td><td><input type='text' id='p_fim' name='p_fim' style='width:30px' value='".$_POST['p_fim']."'></td><td><button type='button' value='p' onclick='enviar(this.value);'>Enviar </button></td></tr>";
$html_camp .= "<tr><td>Segunda Semana: 	</td><td><input type='text' id='s_ini' name='s_ini' style='width:30px' value='".$_POST['s_ini']."'></td><td><input type='text' id='s_fim' name='s_fim' style='width:30px' value='".$_POST['s_fim']."'></td><td><button type='button' value='s' onclick='enviar(this.value);'>Enviar </button></td></tr>";
$html_camp .= "<tr><td>Terceira Semana: </td><td><input type='text' id='t_ini' name='t_ini' style='width:30px' value='".$_POST['t_ini']."'></td><td><input type='text' id='t_fim' name='t_fim' style='width:30px' value='".$_POST['t_fim']."'></td><td><button type='button' value='t' onclick='enviar(this.value);'>Enviar </button></td></tr>";
$html_camp .= "<tr><td>Quarta Semana: 	</td><td><input type='text' id='q_ini' name='q_ini' style='width:30px' value='".$_POST['q_ini']."'></td><td><input type='text' id='q_fim' name='q_fim' style='width:30px' value='".$_POST['q_fim']."'></td><td><button type='button' value='q' onclick='enviar(this.value);'>Enviar </button></td></tr>";
$html_camp .= "<tr><td>Quinta Semana: 	</td><td><input type='text' id='k_ini' name='k_ini' style='width:30px' value='".$_POST['k_ini']."'></td><td><input type='text' id='k_fim' name='k_fim' style='width:30px' value='".$_POST['k_fim']."'></td><td><button type='button' value='k' onclick='enviar(this.value);'>Enviar </button></td></tr>";
$html_camp .= "<tr><td align='right' colspan='4' style='border:0px; padding-right:10px;'><button type='button' value='' onclick='enviar();'>Todos</button></td></tr>";
$html_camp .= "<input type='hidden' value='".$ars_ini."' id='ars_ini' name='ars_ini' />";
$html_camp .= "<input type='hidden' value='".$ars_fim."' id='ars_fim' name='ars_fim' />";
$html_camp .= "</form>";
$html_camp .= "</table>";
$html_camp .= "<table align='center' width='50%' border='1' cellspacing='5' cellpadding='5' style='border-collapse:collapse;font-size:14pt;color:blue;font-family:arial' >";
$n=0;
$html_camp .= "<tr bgcolor='#ebebeb' >";
$html_camp .= "<td align='center' colspan='5' >METAS DE 2018 <br>(" . ('Período entre '.($ars_ini?$ars_ini:'01').'/'.$mes.'/'.$ano.' e '.($ars_fim?$ars_fim:$ultimo_dia).'/'.$mes.'/'.$ano.'') . ")</td>";
$html_camp .= "</tr>";
$html_camp .= "<tr bgcolor='#ebebeb' >";
$html_camp .= "<td align='center'>BANCO</td><td align='center'>ÍNDICE</td><td align='center'>META</td><td align='center' style='background:yellow'>QTD</td><td align='right'>PERCENTUAL</td>";
$html_camp .= "</tr>";
foreach($arr_banco as $b => $c){
	$n++;
	$stl="<tr height='2px'><td colspan='5'></td></tr>";
	foreach($arr_banco[$b] as $d => $e){
		if($n==1){$stl="";}
			$html_camp .= $stl;
			if($arr_meta[$b][$d]>0){
			$html_camp .= "<form method='post' action='dados.php' target='_blank' SCROLLBARS=YES TOOLBAR=NO>";
			$html_camp .= "<tr bgcolor='" . ($e>=100?'#15FF15':'')."' >";
			$html_camp .= "<td align='center'>";
			$html_camp .= "<div style='padding:0px;margin-left:-5px;margin-top:-5px;height:33px;width:" . (($e>=100?90:$e)/2) ."%;z-index:-999;float:left;position:absolute;background:#15FF15'>&nbsp;</div>";
			$html_camp .= $b;
			$html_camp .= "</td>";
			$html_camp .= "<td align='center'>" . $d . "</td>";
			$html_camp .= "<td align='center'>" . ($d=="Faturamento"?number_format($arr_meta[$b][$d],2,",","."):$arr_meta[$b][$d]) . "</td>";
			$html_camp .= "<td align='center' style='background:yellow' onMouseOver='javascript:$(this).css(\"background\",\"#ccc\"); ' onMouseOut='javascript:$(this).css(\"background\",\"yellow\"); '>";
			$html_camp .= "<input type='hidden' value='".$b."' name='banco' />";
			$html_camp .= "<input type='hidden' value='".$d."' name='event' />";
			$html_camp .= "<button type='submit' style='border:0px; background:transparent;font-size:14pt;font-family:arial;cursor:pointer;color:blue'>" . ($d=="Faturamento"?number_format($arr_indic[$b][$d],2,",","."):$arr_indic[$b][$d]) . "</button>";
			$html_camp .= "</td>";
			$html_camp .= "<td align='right'>" . number_format($e,0,"","") . " %</td>";
			$html_camp .= "</tr>";
			$html_camp .= "<input type='hidden' value='".$ars_mes."' name='ars_mes'>";
			$html_camp .= "<input type='hidden' value='".$ars_ano."' name='ars_ano'>";
			$html_camp .= "<input type='hidden' value='".$ars_ini."' id='ars_ini' name='ars_ini' />";
			$html_camp .= "<input type='hidden' value='".$ars_fim."' id='ars_fim' name='ars_fim' />";
			$html_camp .= "</form>";
		}
		$stl="";
	}
}

$html_camp .= "</table>";
echo $html_camp;

?>
</body>
</html>