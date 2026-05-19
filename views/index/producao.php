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
			<label for="startDate">&nbsp;M&ecirc;s / Ano:</label>
			<input type="text" name="startDate" id="startDate" class="date-picker" readonly="readonly" value="<?php echo htmlspecialchars($monthYearLabel, ENT_QUOTES, 'UTF-8'); ?>"/>
			<span id="obg_date"></span>
			<input type="hidden" name="mes" id="mes" class="date-picker" value="<?php echo date('m'); ?>"/>
			<input type="hidden" name="ano" id="ano" class="date-picker" value="<?php echo date('Y'); ?>"/>
			<br><br><br><br><br>
			<div class="icon-wrapper">
				<div class="icon">
					<a href="#" id="frm" onclick="EnviarDados('index.php',4,'')">
						<img src="css/images/header/icon-48-themes.png" alt="" /><span>Relatório</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
