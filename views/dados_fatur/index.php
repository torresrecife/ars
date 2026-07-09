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
<?php $index = 1; ?>
<table align='center' width='70%' id='tbf1' border='1' cellspacing='5' cellpadding='5' bordercolor='#ccc' style='border-collapse:collapse;font-size:10pt;color:#333;font-family:arial;margin-top:20px'>
<tr bgcolor='#ebebeb'>
<th align='center' class='comFiltro'><b>N.</b></th>
<th align='center' class='comFiltro'><b>Código</b></th>
<th align='center' class='comFiltro'><b>Autor</b></th>
<th align='center' class='comFiltro'><b>Réu</b></th>
<th align='center' class='comFiltro'><b>Processo</b></th>
<th align='center' class='comFiltro'><b>Processo CNJ</b></th>
<th align='center' class='comFiltro'><b>Conta</b></th>
<th align='center' class='comFiltro'><b>Comarca</b></th>
<th align='center' class='comFiltro'><b>UF</b></th>
<th align='center' class='comFiltro'><b>Cartório</b></th>
<th align='center' class='comFiltro'><b>Cod Lançamento</b></th>
<th align='center' class='comFiltro'><b>N. Contratante</b></th>
<th align='center' class='comFiltro'><b>Andamento</b></th>
<th align='center' class='comFiltro'><b>Valor</b></th>
<th align='center' class='comFiltro'><b>D.Evento</b></th>
<th align='center' class='comFiltro'><b>D.Cadastro</b></th>
</tr>
<?php foreach ($rows as $row): ?>
<tr>
<td align='center' class='cls_td'><?php echo $index++; ?></td>
<td align='center' class='cls_real' onclick='enviar_neo(<?php echo (int) $row['Codigo']; ?>)'><?php echo $row['Codigo']; ?></td>
<td align='center'><?php echo htmlspecialchars((string) $row['Adverso2'], ENT_QUOTES, 'UTF-8'); ?></td>
<td align='center'><?php echo htmlspecialchars((string) $row['Adverso'], ENT_QUOTES, 'UTF-8'); ?></td>
<td align='center'><?php echo htmlspecialchars((string) $row['processo_exibicao'], ENT_QUOTES, 'UTF-8'); ?></td>
<td align='center'><?php echo htmlspecialchars((string) $row['processo_cnj_exibicao'], ENT_QUOTES, 'UTF-8'); ?></td>
<td align='center'><?php echo htmlspecialchars((string) $row['ContaContratoNeoCobranca'], ENT_QUOTES, 'UTF-8'); ?></td>
<td align='center'><?php echo (string) $row['comarca_exibicao']; ?></td>
<td align='center'><?php echo htmlspecialchars((string) $row['estado_exibicao'], ENT_QUOTES, 'UTF-8'); ?></td>
<td align='center'><?php echo htmlspecialchars((string) $row['Cartorio'], ENT_QUOTES, 'UTF-8'); ?></td>
<td align='center'><?php echo $row['CodigoLancamento']; ?></td>
<td align='center'><?php echo htmlspecialchars((string) $row['IdentificadorContratante'], ENT_QUOTES, 'UTF-8'); ?></td>
<td align='center'><?php echo htmlspecialchars((string) $row['Andamento'], ENT_QUOTES, 'UTF-8'); ?></td>
<td align='right' class='cls_rs'><?php echo number_format((float) $row['valores'], 2, ',', '.'); ?></td>
<td align='right'><?php echo $row['DataEvento']; ?></td>
<td align='right'><?php echo $row['DataCadastro']; ?></td>
</tr>
<?php endforeach; ?>
</table>
<table align='center' width='70%' border='0' cellspacing='2' cellpadding='2' style='border-collapse:collapse;font-size:10pt;color:#333;font-family:arial; font-weight:bold;margin-top:20px'>
<tr>
<td align='left'>Banco: <?php echo htmlspecialchars((string) $bankName, ENT_QUOTES, 'UTF-8'); ?></td>
<td align='left'><span class='titulo_r' id='id_sel'>Total Selecionado: <?php echo $totalCount; ?></span></td>
<td align='right'><div id='id_crs'>Valor Total: <b><?php echo number_format($totalValue, 2, ',', '.'); ?></b></div></td>
<td align='right'>Lançamentos</td>
</tr>
</table>
<br>
</body>
</html>
