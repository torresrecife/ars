<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<label><h2>Produção</h2></label>
			<label for="startSetor">Setor:</label>
			<select name="startSetor" id="startSetor" class="input-default" style="height:20px;width:200px;">
				<option value="">Todas os Setores</option>
				<?php foreach ($areas as $area): ?>
					<option value="<?php echo $area['area_id']; ?>"><?php echo htmlspecialchars($area['area_nome'], ENT_QUOTES, 'UTF-8'); ?></option>
				<?php endforeach; ?>
			</select>
			<?php if (!empty($showRegionSelector)): ?>
				<label for="regiao_id">&nbsp;Regi&atilde;o:</label>
				<select name="regiao_id" id="regiao_id" class="input-default" style="height:20px;width:200px;">
					<option value="0">Todas as Regi&otilde;es</option>
					<?php foreach ($regions as $region): ?>
						<option value="<?php echo (int) $region['regiao_id']; ?>"<?php echo ((int) $selectedRegionId === (int) $region['regiao_id']) ? ' selected="selected"' : ''; ?>><?php echo htmlspecialchars($region['regiao_nome'], ENT_QUOTES, 'UTF-8'); ?></option>
					<?php endforeach; ?>
				</select>
			<?php else: ?>
				<input type="hidden" name="regiao_id" id="regiao_id" value="<?php echo (int) $selectedRegionId; ?>"/>
			<?php endif; ?>
			<label for="startDate">&nbsp;M&ecirc;s / Ano:</label>
			<input type="text" name="startDate" id="startDate" class="date-picker" readonly="readonly" value="<?php echo htmlspecialchars($monthYearLabel, ENT_QUOTES, 'UTF-8'); ?>"/>
			<span id="obg_date"></span>
			<input type="hidden" name="mes" id="mes" value="<?php echo date('m'); ?>"/>
			<input type="hidden" name="ano" id="ano" value="<?php echo date('Y'); ?>"/>
			<br><br><br><br><br>
			<div class="icon-wrapper">
				<div class="icon">
					<a href="#" id="frm" onclick="EnviarPagina('relatorio.php',true,'',''); return false;">
						<img src="css/images/header/icon-48-themes.png" alt="" /><span>Relatório</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
