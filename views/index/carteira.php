<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<label><h2>Carteira</h2></label>
			<label for="startDate">M&ecirc;s / Ano:</label>
			<input type="text" name="startDate" id="startDate" class="date-picker" readonly="readonly" value="<?php echo htmlspecialchars($monthYearLabel, ENT_QUOTES, 'UTF-8'); ?>"/>
			<span id="obg_date"></span>
			<input type="hidden" name="mes" id="mes" class="date-picker" value="<?php echo date('m'); ?>"/>
			<input type="hidden" name="ano" id="ano" class="date-picker" value="<?php echo date('Y'); ?>"/>
			<?php if (!empty($showRegionSelector)): ?>
				<br><br>
				<label for="regiao_id">Regi&atilde;o:</label>
				<select name="regiao_id" id="regiao_id" class="input-default" style="height:20px;width:220px;">
					<option value="0">Todas as Regi&otilde;es</option>
					<?php foreach ($regions as $region): ?>
						<option value="<?php echo (int) $region['regiao_id']; ?>"<?php echo ((int) $selectedRegionId === (int) $region['regiao_id']) ? ' selected="selected"' : ''; ?>><?php echo htmlspecialchars($region['regiao_nome'], ENT_QUOTES, 'UTF-8'); ?></option>
					<?php endforeach; ?>
				</select>
			<?php else: ?>
				<input type="hidden" name="regiao_id" id="regiao_id" value="<?php echo (int) $selectedRegionId; ?>"/>
			<?php endif; ?>
			<br><br><br>
			<?php foreach ($banks as $bank): ?>
				<div class='icon-wrapper' style='height:100%'>
					<div class='icon'>
						<a href='#' onclick='EnviarDados("index.php",2,"<?php echo htmlspecialchars((string) $hidArea, ENT_QUOTES, 'UTF-8'); ?>","<?php echo $bank['banco_id']; ?>")' class='clspet' grupo='0'>
							<img src='css/images/header/icon-48-module.png' alt='' />
							<span style='position:relative;margin-top:0px'> &nbsp; <?php echo htmlspecialchars($bank['banco_name'] . ($bank['banco_class'] ? ' (' . $bank['banco_class'] . ')' : ''), ENT_QUOTES, 'UTF-8'); ?> &nbsp; </span>
						</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
