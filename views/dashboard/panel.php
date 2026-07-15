<?php
if (!empty($viewData['error'])) {
	echo "<br><br><br><br><br>";
	echo "<div style='font-size:18px;height:50px;text-align:center'>" . $viewData['error'] . "</div>";
	echo "<div style='font-size:18px;height:50px;text-align:center'><input type='button' onclick='javascript:window.history.back()' value='Voltar' style='cursor:pointer;height:30px;width:100px'/></div>";
	return;
}

$bank = $viewData['bank'];
$weeks = $viewData['weeks'];
$productionRows = $viewData['productionRows'];
$financialRows = $viewData['financialRows'];
$prejudiceRows = $viewData['prejudiceRows'];
$summary = $viewData['summary'];
$month = $viewData['month'];
$year = $viewData['year'];
$startDate = $viewData['startDate'];
$regionLabel = isset($viewData['regionLabel']) ? $viewData['regionLabel'] : '';
$regionId = isset($viewData['regionId']) ? (int) $viewData['regionId'] : 0;
$areaId = isset($viewData['areaId']) ? (string) $viewData['areaId'] : '';
$bankId = isset($viewData['bankId']) ? (int) $viewData['bankId'] : 0;
$showRegionTabs = !empty($viewData['showRegionTabs']);
$regionTabs = isset($viewData['regionTabs']) ? $viewData['regionTabs'] : array();
$contentHeight = $viewData['contentHeight'];
$weekColumnWidth = (count($weeks) === 5 ? '4%' : '5%');
$splitFinancialTable = !empty($productionRows) && !empty($financialRows);
?>
<br><div style='font-family:arial;margin-left:40px;font-size:10pt;'>Cliente: <b><?php echo $bank['banco_cod']; ?></b><?php echo $showRegionTabs ? '' : $regionLabel; ?> | M&ecirc;s / Ano: <b><?php echo $startDate; ?></b> <a href='#' onclick='send_nav("<?php echo $bankId; ?>","p"); return false;'>&lt;</a> <a href='#' onclick='send_nav("<?php echo $bankId; ?>","n"); return false;'>&gt;</a></div>
<?php if ($showRegionTabs): ?>
<div style='font-family:arial;margin:8px 0 12px 40px;font-size:10pt;'>
	<?php foreach ($regionTabs as $tab): ?>
		<a href='#' onclick='send_region("<?php echo $bankId; ?>","<?php echo (int) $tab['id']; ?>"); return false;' style='display:inline-block;padding:4px 10px;margin-right:6px;border:1px solid #bdbdbd;background:<?php echo !empty($tab['active']) ? '#1C86EE' : '#f1f1f1'; ?>;color:<?php echo !empty($tab['active']) ? '#ffffff' : '#333333'; ?>;text-decoration:none;'><?php echo $tab['label']; ?></a>
	<?php endforeach; ?>
</div>
<?php else: ?>
<br>
<?php endif; ?>
<input type='hidden' name='mes' id='mes' value='<?php echo $month; ?>'/>
<input type='hidden' name='ano' id='ano' value='<?php echo $year; ?>'/>
<input type='hidden' name='regiao_id' id='regiao_id' value='<?php echo $regionId; ?>'/>
<input type='hidden' name='area_id' id='panel_area_id' value='<?php echo htmlspecialchars($areaId, ENT_QUOTES, 'UTF-8'); ?>'/>
<input type='hidden' name='bank_id' id='panel_bank_id' value='<?php echo $bankId; ?>'/>
<script>
	function send_form(andaId,bankId,bankName,month,year,weekKey,detailType){
		$('#detail_bank_id').val(bankId);
		$('#detail_anda_id').val(andaId);
		$('#detail_month').val(month);
		$('#detail_year').val(year);
		$('#detail_week').val(weekKey);
		$('#detail_region_id').val($('#regiao_id').val() || '<?php echo $regionId; ?>');
		$('#banco_and').val(bankName);
		$('#banco_lnc').val(bankName);
		if(detailType=='and'){
			$('#form_ars').attr('action','dados_anda.php');
		}else if(detailType=='fat'){
			$('#form_ars').attr('action','dados_fatur.php');
		}
		$('#form_ars').attr('target','_blank');
		$('#form_ars').submit();
	}
	function send_nav(bankId,valor3){
		var m_mes = $('#mes').val();
		add_month(m_mes,valor3);
		$('#bank_id').val(bankId);
		$('#hid_flag').val(bankId);
		$('#area_id').val($('#panel_area_id').val() || '');
		$('#hid_area').val($('#panel_area_id').val() || '');
		$('#form_ars').attr('action','painel.php');
		$('#form_ars').attr('target','');
		$('#form_ars').submit();
	}
	function send_region(bankId,regiaoId){
		$('#regiao_id').val(regiaoId);
		$('#bank_id').val(bankId);
		$('#hid_flag').val(bankId);
		$('#area_id').val($('#panel_area_id').val() || '');
		$('#hid_area').val($('#panel_area_id').val() || '');
		$('#form_ars').attr('action','painel.php');
		$('#form_ars').attr('target','');
		$('#form_ars').submit();
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
}
.cls_colun_2{
	width:2%;
	background: #FFB90F;
}
.cls_vals{
	width:<?php echo $weekColumnWidth; ?>;
}
.cls_vals2{
	height:25px;
	width:<?php echo $weekColumnWidth; ?>;
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
.cls_red {
    background: #ffdede;
}
.cls_red2 {
    background: #f5d5d5;
}
</style>
<table align='center' height='auto' width='100%' border='0' cellspacing='1' cellpadding='1' id='tb_pro' style='font-family:Tahoma;font-size:8pt; border-collapse: collapse;'>
	<tr>
		<td align='center' rowspan='2' style='border:0;width:5px'></td>
		<td align='center' rowspan='2' class='cls_sema cls_indic'>INDICADOR</td>
		<?php foreach ($weeks as $week): ?>
			<td align='center' colspan='3' class='cls_sema'><?php echo $week['label']; ?></td>
		<?php endforeach; ?>
		<td align='center' colspan='3' rowspan='1' class='cls_sema cls_vals'>TOTAL</td>
	</tr>
	<tr>
		<?php foreach ($weeks as $week): ?>
			<td align='center' class='cls_dados cls_vals'>META</td>
			<td align='center' class='cls_dados cls_vals'>REALIZADO</td>
			<td align='center' class='cls_dados cls_vals'>FAROL</td>
		<?php endforeach; ?>
		<td align='center' class='cls_dados cls_bk2'>META</td>
		<td align='center' class='cls_dados cls_bk2'>REALIZADO</td>
		<td align='center' class='cls_dados cls_bk2'>FAROL</td>
	</tr>
	<?php foreach ($productionRows as $rowIndex => $row): ?>
	<tr style='height:30px'>
		<?php if ($rowIndex === 0): ?>
			<td align='center' rowspan='<?php echo count($productionRows); ?>' class='cls_colun'><div style='color:#FFF;transform: rotate(270deg);width:20px'><b>OPERACAO</b></div></td>
		<?php endif; ?>
		<td class='cls_indic'><?php echo $row['name']; ?></td>
		<?php foreach ($row['weekData'] as $weekIndex => $weekData): ?>
			<td align='center' class='cls_vals' style='background:#F0F0F0'><?php echo number_format($weekData['meta'], 0, ',', '.'); ?></td>
			<td align='center' class='cls_vals cls_real' onclick='send_form("<?php echo (int) $row['andaId']; ?>","<?php echo (int) $bank['banco_id']; ?>","<?php echo addslashes($bank['banco_name']); ?>","<?php echo (int) $month; ?>","<?php echo (int) $year; ?>","<?php echo (int) $weekIndex; ?>","and");'><?php echo $weekData['real']; ?></td>
			<td align='center' class='cls_vals'><img src='http://10.81.11.202/img/<?php echo $weekData['icon']; ?>' class='box' /><?php echo number_format($weekData['percent'], 0, ',', ''); ?>%</td>
		<?php endforeach; ?>
		<td align='center' class='cls_vals cls_real cls_bk' style='background:#F2F5A9'><b><?php echo number_format($row['totalMeta'], 0, ',', '.'); ?></b></td>
		<td align='center' class='cls_vals cls_real cls_bk' onclick='send_form("<?php echo (int) $row['andaId']; ?>","<?php echo (int) $bank['banco_id']; ?>","<?php echo addslashes($bank['banco_name']); ?>","<?php echo (int) $month; ?>","<?php echo (int) $year; ?>","total","and");'><b><?php echo $row['totalReal']; ?></b></td>
		<td align='center' class='cls_vals cls_bk'><img src='http://10.81.11.202/img/<?php echo $row['totalIcon']; ?>' class='box' /><?php echo number_format($row['totalPercent'], 0, ',', ''); ?>%</td>
	</tr>
	<?php endforeach; ?>
<?php if ($splitFinancialTable): ?>
</table>
<?php endif; ?>
<input type='hidden' name='codig_and' id='codig_and' />
<input type='hidden' name='banco_and' id='banco_and' />
<?php if ($splitFinancialTable): ?>
<br><br>
<table align='center' height='20%' width='100%' border='0' cellspacing='1' cellpadding='1' id='tb_fim' style='font-family:Tahoma;font-size:8pt; border-collapse: collapse;'>
<?php endif; ?>
	<?php foreach ($financialRows as $rowIndex => $row): ?>
	<tr style='height:30px'>
		<?php if ($rowIndex === 0): ?>
			<td align='center' rowspan='<?php echo count($financialRows); ?>' class='cls_colun_2'><div style='color:#FFF;transform:rotate(270deg);width:20px;margin-top:20px'><b>FINANCEIRO</b></div></td>
		<?php endif; ?>
		<td class='cls_indic'><?php echo $row['name']; ?></td>
		<?php foreach ($row['weekData'] as $weekIndex => $weekData): ?>
			<td align='center' class='cls_vals' style='background:#F0F0F0'><?php echo number_format($weekData['meta'], 2, ',', '.'); ?></td>
			<td align='center' class='cls_vals cls_real' onclick='send_form("<?php echo (int) $row['andaId']; ?>","<?php echo (int) $bank['banco_id']; ?>","<?php echo addslashes($bank['banco_name']); ?>","<?php echo (int) $month; ?>","<?php echo (int) $year; ?>","<?php echo (int) $weekIndex; ?>","fat");'><?php echo number_format($weekData['real'], 2, ',', '.'); ?></td>
			<td align='center' class='cls_vals'><img src='http://10.81.11.202/img/<?php echo $weekData['icon']; ?>' class='box' /><?php echo number_format($weekData['percent'], 0, ',', ''); ?>%</td>
		<?php endforeach; ?>
		<td align='center' class='cls_vals cls_real cls_bk' style='background:#F2F5A9'><b><?php echo number_format($row['totalMeta'], 2, ',', '.'); ?></b></td>
		<td align='center' class='cls_vals cls_real cls_bk' onclick='send_form("<?php echo (int) $row['andaId']; ?>","<?php echo (int) $bank['banco_id']; ?>","<?php echo addslashes($bank['banco_name']); ?>","<?php echo (int) $month; ?>","<?php echo (int) $year; ?>","total","fat");'><b><?php echo number_format($row['totalReal'], 2, ',', '.'); ?></b></td>
		<td align='center' class='cls_vals cls_bk'><img src='http://10.81.11.202/img/<?php echo $row['totalIcon']; ?>' class='box' /><?php echo number_format($row['totalPercent'], 0, ',', ''); ?>%</td>
	</tr>
	<?php endforeach; ?>
	<tr height='5px'></tr>
	<tr>
		<td style='border: 0px'></td>
		<td class='cls_vals2 cls_bk'><b>TOTAL FINANCEIRO</b></td>
		<?php foreach ($summary['weekTotals'] as $weekTotal): ?>
			<td align='center' class='cls_vals2 cls_bk' style='background:#F2F5A9'><b><?php echo number_format($weekTotal['meta'], 2, ',', '.'); ?></b></td>
			<td align='center' class='cls_vals2 cls_bk'><b><?php echo number_format($weekTotal['real'], 2, ',', '.'); ?></b></td>
			<td align='center' class='cls_vals2 cls_bk'><img src='http://10.81.11.202/img/<?php echo $weekTotal['icon']; ?>' class='box' />&nbsp;<b><?php echo number_format($weekTotal['percent'], 1, ',', ''); ?>%</b></td>
		<?php endforeach; ?>
		<td align='center' class='cls_vals2 cls_bk' style='background:#F2F5A9'><b><?php echo number_format($summary['metaTotal'], 2, ',', '.'); ?></b></td>
		<td align='center' class='cls_vals2 cls_bk'><b><?php echo number_format($summary['realTotal'], 2, ',', '.'); ?></b></td>
		<td align='center' class='cls_vals2 cls_bk'><img src='http://10.81.11.202/img/<?php echo $summary['grandIcon']; ?>' class='box' />&nbsp;<b><?php echo number_format($summary['grandPercent'], 1, ',', ''); ?>%</b></td>
	</tr>
	<tr height='5px'></tr>
	<?php foreach ($prejudiceRows as $row): ?>
	<tr>
		<td style='border: 0px'></td>
		<td class='cls_vals2 cls_red'><b>PREJUIZOS</b></td>
		<?php foreach ($row['weekData'] as $weekIndex => $weekData): ?>
			<td align='center' class='cls_vals2 cls_red2' style='background:#f5d5d5'><b>0,00</b></td>
			<td align='center' class='cls_vals2 cls_real cls_red' onclick='send_form("<?php echo (int) $row['andaId']; ?>","<?php echo (int) $bank['banco_id']; ?>","<?php echo addslashes($bank['banco_name']); ?>","<?php echo (int) $month; ?>","<?php echo (int) $year; ?>","<?php echo (int) $weekIndex; ?>","fat");'><b><?php echo number_format($weekData['real'], 2, ',', '.'); ?></b></td>
			<td align='center' class='cls_vals2 cls_red'><b>-</b></td>
		<?php endforeach; ?>
		<td align='center' class='cls_vals2 cls_red' style='background:#ffdede'><b>0,00</b></td>
		<td align='center' class='cls_vals2 cls_real cls_red' onclick='send_form("<?php echo (int) $row['andaId']; ?>","<?php echo (int) $bank['banco_id']; ?>","<?php echo addslashes($bank['banco_name']); ?>","<?php echo (int) $month; ?>","<?php echo (int) $year; ?>","total","fat");'><b><?php echo number_format($row['totalReal'], 2, ',', '.'); ?></b></td>
		<td align='center' class='cls_vals2 cls_red'><b>-</b></td>
	</tr>
	<?php endforeach; ?>
</table>
<input type='hidden' name='codig_lnc' id='codig_lnc' />
<input type='hidden' name='banco_lnc' id='banco_lnc' />
<input type='hidden' name='detail_bank_id' id='detail_bank_id' />
<input type='hidden' name='detail_anda_id' id='detail_anda_id' />
<input type='hidden' name='detail_month' id='detail_month' />
<input type='hidden' name='detail_year' id='detail_year' />
<input type='hidden' name='detail_week' id='detail_week' />
<input type='hidden' name='detail_region_id' id='detail_region_id' value='<?php echo $regionId; ?>' />
<br>
<table align='center' height='6%' width='25%' border='1' cellspacing='3' cellpadding='3' id='tb_tot' style='font-family:arial;font-size:8pt; border-collapse: collapse;'>
	<tr>
		<td align='center' style='background:#F0F0F0'>R$ <?php echo number_format($summary['metaTotal'], 2, ',', '.'); ?><br></td>
		<td align='center' style='background:#ffffff'>R$ <?php echo number_format($summary['netRealTotal'], 2, ',', '.'); ?><br></td>
		<td align='center' style='background:#ffffff'><img src='http://10.81.11.202/img/<?php echo $summary['netIcon']; ?>' class='box' /><?php echo number_format($summary['netPercent'], 1, ',', ''); ?>%</td>
	</tr>
</table>
<br><br><br><br>
<script>
	var alturaConteudo = <?php echo (int) $contentHeight; ?>;
	var alturaTela = $(window).height() - $("#header-box").outerHeight(true);
	var alturaMinima = Math.max(290, alturaTela);

	$("#content-box").css({
		height: "auto",
		"min-height": Math.max(alturaMinima, alturaConteudo)
	});
	$("#element-box").css("height", alturaTela - 45);
	$("#content-box .adminform").css("height", "auto");
</script>
