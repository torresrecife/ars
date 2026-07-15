<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<label><h2>Setores</h2></label>
			<?php foreach ($areas as $area): ?>
				<div class='icon-wrapper'>
					<div class='icon'>
						<a href='#' onclick='EnviarPagina("carteiras.php",false,"<?php echo $area['area_id']; ?>",""); return false;' class='clspet' grupo='0'>
							<img src='css/images/header/icon-48-section.png' alt='' />
							<span style='position:relative;margin-top:0px'> &nbsp; <?php echo htmlspecialchars($area['area_nome'], ENT_QUOTES, 'UTF-8'); ?> &nbsp; </span>
						</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
