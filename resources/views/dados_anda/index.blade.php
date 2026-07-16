<html height="100%">
<script type="text/javascript" src="js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="js/jFilterXCel2003.js"></script>
<script type="text/javascript" src="js/functions.js"></script>
<script type="text/javascript">
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
@php $index = 1; @endphp
<table align="center" width="80%" id="tbf1" border="1" cellspacing="5" cellpadding="5" bordercolor="#ccc" style="border-collapse:collapse;font-size:10pt;color:#333;font-family:arial;margin-top:20px">
<tr bgcolor="#ebebeb">
<th align="center" class="comFiltro"><b>N.</b></th>
<th align="center" class="comFiltro"><b>Código</b></th>
<th align="center" class="comFiltro"><b>Adverso</b></th>
<th align="center" class="comFiltro"><b>Ajuizamento</b></th>
<th align="center" class="comFiltro"><b>Processo</b></th>
<th align="center" class="comFiltro"><b>Conta</b></th>
<th align="center" class="comFiltro"><b>Comarca</b></th>
<th align="center" class="comFiltro"><b>UF</b></th>
<th align="center" class="comFiltro"><b>Andamento</b></th>
<th align="center" class="comFiltro"><b>D.Evento</b></th>
<th align="center" class="comFiltro"><b>D.Cadastro</b></th>
</tr>
@foreach ($rows as $row)
<tr>
<td align="center" class="cls_td">{{ $index++ }}</td>
<td align="center" class="cls_real" onclick="enviar_neo({{ (int) $row['Codigo'] }})">{{ $row['Codigo'] }}</td>
<td align="center">{{ $row['Adverso'] }}</td>
<td align="center">{{ $row['Ajuizamento'] }}</td>
<td align="center">{{ $row['Processo'] === '' ? '-' : $row['Processo'] }}</td>
<td align="center">{{ $row['ContaContratoNeoCobranca'] }}</td>
<td align="center">{{ $row['comarca_exibicao'] }}</td>
<td align="center">{{ $row['estado_exibicao'] }}</td>
<td align="center">{{ $row['Andamento'] }}</td>
<td align="right">{{ $row['DataEvento'] }}</td>
<td align="right">{{ $row['DataCadastro'] }}</td>
</tr>
@endforeach
</table>
<table align="center" width="80%" border="0" cellspacing="2" cellpadding="2" style="border-collapse:collapse;font-size:10pt;color:#333;font-family:arial; font-weight:bold;margin-top:20px">
<tr>
<td align="left">Banco: {{ $bankName }}</td>
<td align="left"><span class="titulo_r" id="id_sel">Total Selecionado: {{ $totalCount }}</span></td>
<td align="right">Andamentos</td>
</tr>
</table>
<br>
</body>
</html>
