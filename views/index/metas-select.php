<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<label><h2>Administrar Metas</h2></label>
			<label for="startBanco">Banco:</label>
			<select name="startBanco" id="startBanco" class="input-default" style="height:20px;width:200px;">
				<option></option>
				<?php foreach ($banks as $bank): ?>
					<option value="<?php echo $bank['banco_id']; ?>"><?php echo htmlspecialchars($bank['banco_name'] . ' (' . $bank['banco_class'] . ')', ENT_QUOTES, 'UTF-8'); ?></option>
				<?php endforeach; ?>
			</select>
			<label for="startDate">M&ecirc;s / Ano:</label>
			<input type="text" name="startDate" id="startDate" class="date-picker" readonly="readonly" value="<?php echo htmlspecialchars($monthYearLabel, ENT_QUOTES, 'UTF-8'); ?>" style="font-family:Tahoma"/>
			<span id="obg_date"></span>
			<input type="hidden" name="mes" id="mes" value="<?php echo date('m'); ?>"/>
			<input type="hidden" name="ano" id="ano" value="<?php echo date('Y'); ?>"/>
			<br><br><br><br><br>
			<div class="icon-wrapper">
				<div class="icon">
					<a href="#" id="frm" onclick="EnviarDados('index.php',14,'')">
						<img src="css/images/header/icon-48-themes.png" alt="" /><span>Metas</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
