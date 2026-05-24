<meta http-equiv='content-type' content='text/html; charset=utf-8'>
<button name='geral' value='0' style='float:right;margin-right:50px;border:1px dotted #999;'>Semanal</button>
<br><div style='font-family:arial;margin-left:40px;font-size:10pt;'><?php echo $titleArea; ?> | Mês / Ano: <b><?php echo htmlspecialchars($startDate, ENT_QUOTES, 'UTF-8'); ?></b> </div><br>
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
td.cls_dados{border-left-width:1px;border-botton:1px dotted #999;border-left:1px dotted #999;border-right:1px dotted #999;height:30px;}
td.cls_body{border-left-width:1px;border:1px dotted #999;height:30px;}
.cls_dados{background:#DBDBDB;}
.cls_sema{background:#ccc;font-weight:bold;}
.cls_vals{width:6%;}
.cls_indic{height:30px;width:13%;border:1px dotted #999;}
.cls_indic2{height:30px;width:3%;border:1px dotted #999;}
.cls_perc{width:3%;}
.cls_perc2{width:0.5%;}
.cls_real{font-weight:bold;color:#8B4513;cursor:pointer;}
.cls_real:hover{background:#ebebeb;}
</style>
<table align="center" height="50%" width="60%" border="0" cellspacing='3' cellpadding='3' style="font-family:arial;font-size:8pt; border-collapse: collapse;background:#ffffff">
	<tr>
		<td align="center" colspan="6" class="cls_indic">PRODUCAO - BVAA</td>
		<td align="center" colspan="0"></td>
		<td align="center" colspan="1" class="cls_indic2">TOTAL</td>
	</tr>
	<tr>
		<td align="center" class="cls_dados cls_vals">CLIENTE</td>
		<td align="center" class="cls_dados cls_vals">META/MES</td>
		<td align="center" class="cls_dados cls_vals">META/HOJE</td>
		<td align="center" class="cls_dados cls_vals">REALIZADO</td>
		<td align="center" class="cls_dados cls_vals">SALDO (Parcial)</td>
		<td align="center" class="cls_dados cls_perc">PERC / HOJE</td>
		<td align="center" class="cls_perc2">&nbsp;</td>
		<td align="center" class="cls_dados cls_perc">PERC / MES</td>
	</tr>
	<?php foreach ($rows as $row): ?>
	<tr style='height:30px'>
		<td class='cls_body' style='padding-left:5px'><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
		<td class='cls_body' align='center'>R$ <?php echo number_format($row['metaMonth'], 2, ',', '.'); ?></td>
		<td class='cls_body' align='center'>R$ <?php echo number_format($row['metaToday'], 2, ',', '.'); ?></td>
		<td class='cls_body cls_real' align='center' onclick='send_form("<?php echo implode(',', $row['codes']); ?>","<?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>");'>R$ <?php echo number_format($row['realized'], 2, ',', '.'); ?></td>
		<td class='cls_body' align='center'>R$ <?php echo number_format($row['balance'], 2, ',', '.'); ?></td>
		<td class='cls_body' align='center' style='background:<?php echo $row['color']; ?>;color:#000'><?php echo number_format($row['percentToday'], 1, ',', '.'); ?>%</td>
		<td class='cls_perc2' align='center' style='background:#fff;color:#000;border-botton:0;border-top:0;'>&nbsp;</td>
		<td class='cls_body' align='center' style='background:<?php echo $row['color']; ?>;color:#000'><b><?php echo number_format($row['percentMonth'], 1, ',', '.'); ?>%</b></td>
	</tr>
	<?php endforeach; ?>
	<input type='hidden' name='codig_lnc' id='codig_lnc' />
	<input type='hidden' name='banco_lnc' id='banco_lnc' />
	<input type='hidden' name='startSetor' value='<?php echo htmlspecialchars($startSector, ENT_QUOTES, 'UTF-8'); ?>'/>
	<input type='hidden' name='startDate' value='<?php echo htmlspecialchars($startDate, ENT_QUOTES, 'UTF-8'); ?>' />
	<input type='hidden' name='mes' value='<?php echo (int) $month; ?>' />
	<input type='hidden' name='ano' value='<?php echo (int) $year; ?>' />
	<tr><td colspan='8'></td></tr>
	<tr class='cls_dados'>
		<td align='center' class='cls_body'><b>TOTAIS</b></td>
		<td align='center' class='cls_body'><b>R$ <?php echo number_format($totals['metaMonth'], 2, ',', '.'); ?></b></td>
		<td align='center' class='cls_body'><b>R$ <?php echo number_format($totals['metaToday'], 2, ',', '.'); ?></b></td>
		<td align='center' class='cls_body'><b>R$ <?php echo number_format($totals['realized'], 2, ',', '.'); ?></b></td>
		<td align='center' class='cls_body'><b>R$ <?php echo number_format($totals['balance'], 2, ',', '.'); ?></b></td>
		<td align='center' class='cls_perc' style='background:<?php echo $totals['color']; ?>'><b><?php echo number_format($totals['percentToday'], 1, ',', '.'); ?>%</b></td>
		<td align='center' class='cls_perc2'>&nbsp;</td>
		<td align='center' class='cls_perc' style='background:<?php echo $totals['color']; ?>'><b><?php echo number_format($totals['percentMonth'], 1, ',', '.'); ?>%</b></td>
	</tr>
</table>
<style>
#content-box{
	height:<?php echo (int) $contentHeight; ?>px;
}
</style>
