<div class="content_body">
	<div class="cpanel-left">
		<div class="cpanel">
			<label><h2>{{ __('Wallet(s)') }}</h2></label>
			@foreach ($banks as $bank)
				<div class="icon-wrapper icon-wrapper--full-height">
					<div class="icon">
						<a href="#" onclick="AbrirPainel('{{ e($hidArea) }}','{{ (int) $bank['banco_id'] }}'); return false;" class="clspet" grupo="0">
							<img src="css/images/header/icon-48-module.png" alt="" />
							<span class="icon-label">&nbsp; {{ e($bank['banco_name']) }} &nbsp;</span>
						</a>
					</div>
				</div>
			@endforeach
		</div>
	</div>
	<div class="cpanel-right">
		<div class="cpanel">
			@if ($userLevel === 'ADM')
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('index') }}"><img src="css/images/header/icon-48-frontpage.png" alt="" /><span>{{ __('Home') }}</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('usuarios') }}"><img src="css/images/header/icon-48-user.png" alt="" /><span>{{ __('Users') }}</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('setores') }}"><img src="css/images/header/icon-48-move.png" alt="" /><span>{{ __('Sectors') }}</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('clientes') }}"><img src="css/images/header/icon-48-module.png" alt="" /><span>{{ __('Clients') }}</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('andamentos') }}"><img src="css/images/header/icon-48-stats.png" alt="" /><span>{{ __('Progress') }}</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('regioes') }}"><img src="css/images/header/icon-48-regiao.png" alt="" /><span>{{ __('Regions') }}</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('semanas') }}"><img src="css/images/header/icon-48-calendar.png" alt="" /><span>{{ __('Weeks') }}</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('metas') }}"><img src="css/images/header/icon-48-levels.png" alt="" /><span>{{ __('Goals') }}</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('producao') }}"><img src="css/images/header/icon-48-menumgr.png" alt="" /><span>{{ __('Production') }}</span></a></div></div>
			@elseif ($userLevel === 'GER')
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('metas') }}"><img src="css/images/header/icon-48-levels.png" alt="" /><span>{{ __('Goals') }}</span></a></div></div>
				<div class="icon-wrapper"><div class="icon"><a href="{{ url('producao') }}"><img src="css/images/header/icon-48-menumgr.png" alt="" /><span>{{ __('Production') }}</span></a></div></div>
			@endif
		</div>
	</div>
</div>
