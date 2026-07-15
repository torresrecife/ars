<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<label><h2>Carteira(s)</h2></label>
			@foreach ($banks as $bank)
				<div class="icon-wrapper" style="height:100%">
					<div class="icon">
						<a href="#" onclick="AbrirPainel('{{ e($hidArea) }}','{{ (int) $bank['banco_id'] }}'); return false;" class="clspet" grupo="0">
							<img src="css/images/header/icon-48-module.png" alt="" />
							<span style="position:relative;margin-top:0px">&nbsp; {{ e($bank['banco_name']) }} &nbsp;</span>
						</a>
					</div>
				</div>
			@endforeach
		</div>
	</div>
	<div class="cpanel-right">
		<div class="cpanel">
			@if ($userLevel === 'ADM')
				<div class="icon-wrapper"><div class="icon"><a href="index.php"><img src="css/images/header/icon-48-frontpage.png" alt="" /><span>In&iacute;cio</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="usu.php"><img src="css/images/header/icon-48-user.png" alt="" /><span>Usu&aacute;rios</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="setor.php"><img src="css/images/header/icon-48-move.png" alt="" /><span>Setores</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="clientes.php"><img src="css/images/header/icon-48-module.png" alt="" /><span>Clientes</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="andamentos.php"><img src="css/images/header/icon-48-stats.png" alt="" /><span>Andamentos</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="regioes.php"><img src="css/images/header/icon-48-regiao.png" alt="" /><span>Regi&otilde;es</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="semanas.php"><img src="css/images/header/icon-48-calendar.png" alt="" /><span>Semanas</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="metas.php"><img src="css/images/header/icon-48-levels.png" alt="" /><span>Metas</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="producao.php"><img src="css/images/header/icon-48-menumgr.png" alt="" /><span>Produ&ccedil;&atilde;o</span></a></div></div>
			@elseif ($userLevel === 'GER')
				<div class="icon-wrapper"><div class="icon"><a href="metas.php"><img src="css/images/header/icon-48-levels.png" alt="" /><span>Metas</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="producao.php"><img src="css/images/header/icon-48-menumgr.png" alt="" /><span>Produ&ccedil;&atilde;o</span></a></div></div>
			@endif
		</div>
	</div>
</div>
