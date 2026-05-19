<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<label><h2>Carteira</h2></label>
			<label for="startDate">M&ecirc;s / Ano:</label>
			<input type="text" name="startDate" id="startDate" class="date-picker" readonly="readonly" value="<?php echo htmlspecialchars($monthYearLabel, ENT_QUOTES, 'UTF-8'); ?>"/>
			<span id="obg_date"></span>
			<input type="hidden" name="mes" id="mes" class="date-picker" value="<?php echo date('m'); ?>"/>
			<input type="hidden" name="ano" id="ano" class="date-picker" value="<?php echo date('Y'); ?>"/>
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
