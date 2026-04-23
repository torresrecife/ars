<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<?php include "carteiras.php"; ?>
		</div>
	</div>
	<div class="cpanel-right">
		<div class="cpanel">
			<?php 
			if($_SESSION['usuarioNivel']=="ADM"){
				?>
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
				<?php 
			}elseif($_SESSION['usuarioNivel']=="GER"){
				?>
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
				<?php 
			}
			?>
		</div>
	</div>
	<!--div class="cpanel-right-sub">
		<div class="cpanel">
			<div class="icon-wrapper">
				<div class="icon">
					<a href="#" id="frm" onclick="mark_edit(7,0)">
						<img src="css/images/header/icon-48-themes.png" alt=""  /><span>Formulário</span>
					</a>
				</div>
			</div>	
			<div class="icon-wrapper">
				<div class="icon">
					<a href="#" id="prg" onclick="mark_edit(6,0)">
						<img src="css/images/header/icon-48-article-edit.png" alt=""  /><span>Parágrafos</span>
					</a>
				</div>
			</div>
			<div class="icon-wrapper">
				<div class="icon">
					<a href="#" id="prg" onclick="mark_edit('',1)">
						<img src="css/images/header/icon-48-deny.png" alt=""  /><span>Excluir</span>
					</a>
				</div>
			</div>		
		</div>
	</div-->	
</div>