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
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('index') }}"><img src="css/images/header/icon-48-frontpage.png" alt="" /><span>In&iacute;cio</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('usuarios') }}"><img src="css/images/header/icon-48-user.png" alt="" /><span>Usu&aacute;rios</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('setores') }}"><img src="css/images/header/icon-48-move.png" alt="" /><span>Setores</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('clientes') }}"><img src="css/images/header/icon-48-module.png" alt="" /><span>Clientes</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('andamentos') }}"><img src="css/images/header/icon-48-stats.png" alt="" /><span>Andamentos</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('regioes') }}"><img src="css/images/header/icon-48-regiao.png" alt="" /><span>Regi&otilde;es</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('semanas') }}"><img src="css/images/header/icon-48-calendar.png" alt="" /><span>Semanas</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('metas') }}"><img src="css/images/header/icon-48-levels.png" alt="" /><span>Metas</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('producao') }}"><img src="css/images/header/icon-48-menumgr.png" alt="" /><span>Produ&ccedil;&atilde;o</span></a></div></div>
			@elseif ($userLevel === 'GER')
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('metas') }}"><img src="css/images/header/icon-48-levels.png" alt="" /><span>Metas</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('producao') }}"><img src="css/images/header/icon-48-menumgr.png" alt="" /><span>Produ&ccedil;&atilde;o</span></a></div></div>
			@endif
		</div>
	</div>
</div>
