<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<label><h2>Carteira(s)</h2></label>
			<?php foreach ($banks as $bank): ?>
				<div class="icon-wrapper" style="height:100%">
					<div class="icon">
						<a href="#" onclick="EnviarDados('index.php',2,'<?php echo htmlspecialchars($hidArea, ENT_QUOTES, 'UTF-8'); ?>','<?php echo (int) $bank['banco_id']; ?>')" class="clspet" grupo="0">
							<img src="css/images/header/icon-48-module.png" alt="" />
							<span style="position:relative;margin-top:0px">&nbsp; <?php echo htmlspecialchars($bank['banco_name'], ENT_QUOTES, 'UTF-8'); ?> &nbsp;</span>
						</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="cpanel-right">
		<div class="cpanel">
			<?php if ($userLevel === 'ADM'): ?>
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" onclick="EnviarDados('index.php','','','')">
							<img src="css/images/header/icon-48-frontpage.png" alt="" /><span>Início</span>
						</a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" onclick="EnviarDados('index.php','8','','')">
							<img src="css/images/header/icon-48-user.png" alt="" /><span>Usuários</span>
						</a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" onclick="EnviarDados('index.php','9','','')">
							<img src="css/images/header/icon-48-move.png" alt="" /><span>Setores</span>
						</a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" onclick="EnviarDados('index.php','11','')">
							<img src="css/images/header/icon-48-module.png" alt="" /><span>Clientes</span>
						</a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" onclick="EnviarDados('index.php','12','','')">
							<img src="css/images/header/icon-48-stats.png" alt="" /><span>Andamentos</span>
						</a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" onclick="EnviarDados('index.php','16','','')">
							<img src="css/images/header/icon-48-regiao.png" alt="" /><span>Regiões</span>
						</a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" onclick="EnviarDados('index.php','15','','')">
							<img src="css/images/header/icon-48-calendar.png" alt="" /><span>Semanas</span>
						</a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" onclick="EnviarDados('index.php','13','','')">
							<img src="css/images/header/icon-48-levels.png" alt="" /><span>Metas</span>
						</a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" onclick="EnviarDados('index.php','3','','')">
							<img src="css/images/header/icon-48-menumgr.png" alt="" /><span>Produção</span>
						</a>
					</div>
				</div>
			<?php elseif ($userLevel === 'GER'): ?>
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" onclick="EnviarDados('index.php','13','','')">
							<img src="css/images/header/icon-48-levels.png" alt="" /><span>Metas</span>
						</a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a href="#" onclick="EnviarDados('index.php','3','','')">
							<img src="css/images/header/icon-48-menumgr.png" alt="" /><span>Produção</span>
						</a>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
