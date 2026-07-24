@php
	$setoresJsVersion = is_file(base_path('js/modules/setores.js')) ? filemtime(base_path('js/modules/setores.js')) : time();
	$usuariosJsVersion = is_file(base_path('js/modules/usuarios.js')) ? filemtime(base_path('js/modules/usuarios.js')) : time();
	$semanasJsVersion = is_file(base_path('js/modules/semanas.js')) ? filemtime(base_path('js/modules/semanas.js')) : time();
	$regioesJsVersion = is_file(base_path('js/modules/regioes.js')) ? filemtime(base_path('js/modules/regioes.js')) : time();
	$clientesJsVersion = is_file(base_path('js/modules/clientes.js')) ? filemtime(base_path('js/modules/clientes.js')) : time();
	$andamentosJsVersion = is_file(base_path('js/modules/andamentos.js')) ? filemtime(base_path('js/modules/andamentos.js')) : time();
	$metasJsVersion = is_file(base_path('js/modules/metas.js')) ? filemtime(base_path('js/modules/metas.js')) : time();
	$helpersJsVersion = is_file(base_path('js/modules/helpers.js')) ? filemtime(base_path('js/modules/helpers.js')) : time();
	$painelJsVersion = is_file(base_path('js/modules/painel.js')) ? filemtime(base_path('js/modules/painel.js')) : time();
	$relatorioJsVersion = is_file(base_path('js/modules/relatorio.js')) ? filemtime(base_path('js/modules/relatorio.js')) : time();
	$compiledModulesAvailable = is_file(public_path('mix-manifest.json')) && is_file(public_path('build/js/ars-modules.js'));
	$entryUrl = isset($entryUrl) ? (string) $entryUrl : url('index');
	$arsTranslations = [
		'Error in operation.' => __('Error in operation.'),
		'Error loading data.' => __('Error loading data.'),
		'Unable to load data.' => __('Unable to load data.'),
		'Invalid e-mail.' => __('Invalid e-mail.'),
		'Enter the password and password confirmation.' => __('Enter the password and password confirmation.'),
		'The passwords do not match.' => __('The passwords do not match.'),
		'The password must have at least 4 characters.' => __('The password must have at least 4 characters.'),
		'Enter the month / year!' => __('Enter the month / year!'),
		'Save' => __('Save'),
		'Exit' => __('Exit'),
		'Close' => __('Close'),
		'Yes' => __('Yes'),
		'No' => __('No'),
		'Remove' => __('Remove'),
		'The field :field is required.' => __('The field :field is required.'),
		'New Region' => __('New Region'),
		'Edit Region' => __('Edit Region'),
		'Create a new region' => __('Create a new region'),
		'Edit the region below' => __('Edit the region below'),
		'Region created successfully.' => __('Region created successfully.'),
		'Region updated successfully.' => __('Region updated successfully.'),
		'Region deleted successfully.' => __('Region deleted successfully.'),
		'Select a state to add.' => __('Select a state to add.'),
		'This state is already linked to the region.' => __('This state is already linked to the region.'),
		'Select at least one state for the region.' => __('Select at least one state for the region.'),
		'Error saving region.' => __('Error saving region.'),
		'Error loading region data.' => __('Error loading region data.'),
		'Error deleting region.' => __('Error deleting region.'),
		'Do you really want to delete the region :name?' => __('Do you really want to delete the region :name?'),
		'New Progress' => __('New Progress'),
		'Edit Progress' => __('Edit Progress'),
		'Create a new progress' => __('Create a new progress'),
		'Edit the progress below' => __('Edit the progress below'),
		'No linked progress items.' => __('No linked progress items.'),
		'Loading linked progress items...' => __('Loading linked progress items...'),
		'Select a progress item to add.' => __('Select a progress item to add.'),
		'This progress item is already linked.' => __('This progress item is already linked.'),
		'Select at least one linked progress item.' => __('Select at least one linked progress item.'),
		'Progress created successfully.' => __('Progress created successfully.'),
		'Field updated successfully.' => __('Field updated successfully.'),
		'Progress deleted successfully.' => __('Progress deleted successfully.'),
		'Error saving progress.' => __('Error saving progress.'),
		'Error loading progress data.' => __('Error loading progress data.'),
		'Error deleting progress.' => __('Error deleting progress.'),
		'Do you really want to delete the progress :name?' => __('Do you really want to delete the progress :name?'),
		'New Goal' => __('New Goal'),
		'Edit Goal' => __('Edit Goal'),
		'Create a new goal' => __('Create a new goal'),
		'Edit the goal below' => __('Edit the goal below'),
		'Goal(s) created successfully.' => __('Goal(s) created successfully.'),
		'Goal edited successfully.' => __('Goal edited successfully.'),
		'Goal deleted successfully.' => __('Goal deleted successfully.'),
		'Error saving goal.' => __('Error saving goal.'),
		'Error loading goal data.' => __('Error loading goal data.'),
		'Error deleting goal.' => __('Error deleting goal.'),
		'Do you really want to delete the goal :name?' => __('Do you really want to delete the goal :name?'),
		'Goal' => __('Goal'),
		'Total goal' => __('Total goal'),
		'Manual definition' => __('Manual definition'),
		'Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday' => __('Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'),
		'Su,Mo,Tu,We,Th,Fr,Sa' => __('Su,Mo,Tu,We,Th,Fr,Sa'),
		'Sun,Mon,Tue,Wed,Thu,Fri,Sat' => __('Sun,Mon,Tue,Wed,Thu,Fri,Sat'),
		'January,February,March,April,May,June,July,August,September,October,November,December' => __('January,February,March,April,May,June,July,August,September,October,November,December'),
		'Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec' => __('Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec'),
		'Next' => __('Next'),
		'Previous' => __('Previous'),
		'Current month' => __('Current month'),
		'New User' => __('New User'),
		'Edit User' => __('Edit User'),
		'Create a new user' => __('Create a new user'),
		'Edit the user below' => __('Edit the user below'),
		'User created successfully.' => __('User created successfully.'),
		'Select at least one client for the user.' => __('Select at least one client for the user.'),
		'Error saving user.' => __('Error saving user.'),
		'Error loading user data.' => __('Error loading user data.'),
		'Select a client to add.' => __('Select a client to add.'),
		'This client is already linked to the user.' => __('This client is already linked to the user.'),
		'Select a region to add.' => __('Select a region to add.'),
		'Standard user can have only one linked region.' => __('Standard user can have only one linked region.'),
		'This region is already linked to the user.' => __('This region is already linked to the user.'),
		'Do you really want to delete the user :name?' => __('Do you really want to delete the user :name?'),
		'Error deleting user.' => __('Error deleting user.'),
		'User deleted successfully.' => __('User deleted successfully.'),
		'New Client' => __('New Client'),
		'Edit Client' => __('Edit Client'),
		'Create a new client' => __('Create a new client'),
		'Edit the client below' => __('Edit the client below'),
		'Client created successfully.' => __('Client created successfully.'),
		'Client updated successfully.' => __('Client updated successfully.'),
		'Select at least one wallet for the client.' => __('Select at least one wallet for the client.'),
		'Error saving client.' => __('Error saving client.'),
		'Error loading client data.' => __('Error loading client data.'),
		'Select a wallet to add.' => __('Select a wallet to add.'),
		'This wallet is already linked to the client.' => __('This wallet is already linked to the client.'),
		'Do you really want to delete the client :name?' => __('Do you really want to delete the client :name?'),
		'Error deleting client.' => __('Error deleting client.'),
		'Client deleted successfully.' => __('Client deleted successfully.'),
		'New Sector' => __('New Sector'),
		'Edit Sector' => __('Edit Sector'),
		'Create a new sector' => __('Create a new sector'),
		'Edit the sector below' => __('Edit the sector below'),
		'Sector created successfully.' => __('Sector created successfully.'),
		'Error saving sector.' => __('Error saving sector.'),
		'Error loading sector data.' => __('Error loading sector data.'),
		'Do you really want to delete the sector :name?' => __('Do you really want to delete the sector :name?'),
		'Error deleting sector.' => __('Error deleting sector.'),
		'Sector deleted successfully.' => __('Sector deleted successfully.'),
	];
	$currentSection = $pageData->currentSection();
	$currentState = $pageData->state();
	$currentUser = $pageData->user();
	$currentMonthLabel = $pageData->monthYearLabel();
	$pageTitleMap = [
		'inicio' => __('Home'),
		'carteiras' => __('Wallet(s)'),
		'painel' => __('Panel'),
		'producao' => __('Production'),
		'relatorio-semanal' => __('Weekly'),
		'relatorio-mensal' => __('Monthly'),
		'admin' => __('Admin'),
		'usuarios' => __('Users'),
		'setores' => __('Sectors'),
		'clientes' => __('Clients'),
		'andamentos' => __('Progress'),
		'metas-select' => __('Goals'),
		'metas-admin' => __('Goals'),
		'semanas' => __('Weeks'),
		'regioes' => __('Regions'),
	];
	$currentPageTitle = isset($pageTitleMap[$currentSection]) ? $pageTitleMap[$currentSection] : __('ARS Control');
	$contentCardClass = in_array($currentSection, array('andamentos', 'usuarios', 'clientes', 'regioes', 'setores', 'semanas', 'metas-select', 'metas-admin'), true)
		? 'ars-shell__content-card ars-shell__content-card--flat'
		: 'ars-shell__content-card';
	$sectionSubtitle = in_array($currentSection, ['painel', 'producao', 'relatorio-semanal', 'relatorio-mensal', 'carteiras', 'metas-select', 'metas-admin'], true)
		? $currentMonthLabel
		: __('Administrative workspace');
	$isHomeActive = $currentSection === 'inicio';
	$isGoalsActive = in_array($currentSection, ['metas-select', 'metas-admin'], true);
	$isReportActive = in_array($currentSection, ['relatorio-semanal', 'relatorio-mensal'], true);
	$baseQuery = [
		'startDate' => $currentState->startDate(),
		'mes' => $currentState->mes(),
		'ano' => $currentState->ano(),
		'regiao_id' => $currentState->regiaoId(),
	];
	$buildShellUrl = function ($path, array $params = []) {
		$filtered = array_filter($params, function ($value) {
			return $value !== '' && $value !== null;
		});

		return empty($filtered) ? url($path) : url($path) . '?' . http_build_query($filtered);
	};
	$navLinks = [
		['key' => 'inicio', 'label' => __('Home'), 'url' => url('index'), 'active' => $isHomeActive],
		['key' => 'carteiras', 'label' => __('Wallet(s)'), 'url' => $buildShellUrl('carteiras', $baseQuery + ['area_id' => $currentState->areaId()]), 'active' => $currentSection === 'carteiras'],
		['key' => 'painel', 'label' => __('Panel'), 'url' => $buildShellUrl('painel', $baseQuery + ['area_id' => $currentState->areaId(), 'bank_id' => $currentState->bankId()]), 'active' => $currentSection === 'painel'],
		['key' => 'producao', 'label' => __('Production'), 'url' => $buildShellUrl('producao', $baseQuery + ['startSetor' => $currentState->startSetor()]), 'active' => $currentSection === 'producao'],
		['key' => 'relatorio', 'label' => __('Report'), 'url' => $buildShellUrl('relatorio', $baseQuery + ['startSetor' => $currentState->startSetor(), 'geral' => $currentSection === 'relatorio-semanal' ? 1 : 0]), 'active' => $isReportActive],
	];
	$adminLinks = [
		['key' => 'admin', 'label' => __('Admin'), 'url' => url('admin'), 'active' => $currentSection === 'admin'],
		['key' => 'metas', 'label' => __('Goals'), 'url' => url('metas'), 'active' => $isGoalsActive],
		['key' => 'usuarios', 'label' => __('Users'), 'url' => url('usuarios'), 'active' => $currentSection === 'usuarios'],
		['key' => 'clientes', 'label' => __('Clients'), 'url' => url('clientes'), 'active' => $currentSection === 'clientes'],
		['key' => 'setores', 'label' => __('Sectors'), 'url' => url('setores'), 'active' => $currentSection === 'setores'],
		['key' => 'andamentos', 'label' => __('Progress'), 'url' => url('andamentos'), 'active' => $currentSection === 'andamentos'],
		['key' => 'regioes', 'label' => __('Regions'), 'url' => url('regioes'), 'active' => $currentSection === 'regioes'],
		['key' => 'semanas', 'label' => __('Weeks'), 'url' => url('semanas'), 'active' => $currentSection === 'semanas'],
	];
	$shellIcons = [
		'inicio' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/></svg>',
		'carteiras' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h18v12H3z"/><path d="M3 10h18"/><path d="M16 15h3"/></svg>',
		'painel' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 13h4v7H4z"/><path d="M10 4h4v16h-4z"/><path d="M16 9h4v11h-4z"/></svg>',
		'producao' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 18h16"/><path d="M7 18V8"/><path d="M12 18V4"/><path d="M17 18v-6"/></svg>',
		'relatorio' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v6h6"/><path d="M10 13h6"/><path d="M10 17h6"/></svg>',
		'admin' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 4 7v5c0 5 3.5 8 8 9 4.5-1 8-4 8-9V7z"/><path d="M9.5 12.5 11 14l3.5-4"/></svg>',
		'metas' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a9 9 0 1 0 9 9"/><path d="M21 3 12 12"/><path d="M16 3h5v5"/></svg>',
		'usuarios' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="3.5"/><path d="M17 11a3 3 0 1 0 0-6"/><path d="M21 21v-2a4 4 0 0 0-3-3.87"/></svg>',
		'clientes' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16v12H4z"/><path d="M8 7V5h8v2"/><path d="M4 12h16"/></svg>',
		'setores' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h7v7H4z"/><path d="M13 4h7v7h-7z"/><path d="M4 13h7v7H4z"/><path d="M13 13h7v7h-7z"/></svg>',
		'andamentos' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 6h14"/><path d="M5 12h14"/><path d="M5 18h10"/></svg>',
		'regioes' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11.5 12 4l9 7.5-9 8-9-8z"/><path d="M12 4v15.5"/></svg>',
		'semanas' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3v4"/><path d="M17 3v4"/><path d="M4 9h16"/><rect x="4" y="5" width="16" height="16" rx="2"/></svg>',
		'back' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 18 9 12l6-6"/></svg>',
		'logout' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17H5V7h5"/><path d="M14 8l4 4-4 4"/><path d="M18 12H9"/></svg>',
		'menu' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h16"/></svg>',
		'action' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14"/><path d="M5 12h14"/></svg>',
	];
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br" dir="ltr">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ __('ARS Control') }}</title>
	<link href="{{ asset('css/images/favicon.ico') }}" rel="shortcut icon" type="image/vnd.microsoft.icon" />
	<link rel="stylesheet" href="{{ asset('css/template.css') }}" type="text/css" />
	<link rel="stylesheet" href="{{ asset('css/custom-theme/jquery-ui.css') }}">
	@if (is_file(public_path('mix-manifest.json')) && is_file(public_path('build/css/app.css')))
		<link rel="stylesheet" href="{{ asset('public/' . ltrim(mix('/build/css/app.css'), '/')) }}">
	@else
		<link rel="stylesheet" href="{{ asset('css/ars-modern.css') }}">
	@endif
	<script type="text/javascript" src="{{ asset('js/jquery-1.8.0.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/jquery-ui-1.8.23.custom.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/jquery.meio.mask.js') }}"></script>
	<script type="text/javascript">
	window.arsTranslations = {!! json_encode($arsTranslations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
	</script>
	@if ($compiledModulesAvailable)
		<script type="text/javascript" src="{{ asset('public/' . ltrim(mix('/build/js/ars-modules.js'), '/')) }}"></script>
	@else
		<script type="text/javascript" src="{{ asset('js/modules/helpers.js') }}?v={{ $helpersJsVersion }}"></script>
		<script type="text/javascript" src="{{ asset('js/modules/setores.js') }}?v={{ $setoresJsVersion }}"></script>
		<script type="text/javascript" src="{{ asset('js/modules/usuarios.js') }}?v={{ $usuariosJsVersion }}"></script>
		<script type="text/javascript" src="{{ asset('js/modules/semanas.js') }}?v={{ $semanasJsVersion }}"></script>
		<script type="text/javascript" src="{{ asset('js/modules/regioes.js') }}?v={{ $regioesJsVersion }}"></script>
		<script type="text/javascript" src="{{ asset('js/modules/clientes.js') }}?v={{ $clientesJsVersion }}"></script>
		<script type="text/javascript" src="{{ asset('js/modules/andamentos.js') }}?v={{ $andamentosJsVersion }}"></script>
		<script type="text/javascript" src="{{ asset('js/modules/metas.js') }}?v={{ $metasJsVersion }}"></script>
		<script type="text/javascript" src="{{ asset('js/modules/painel.js') }}?v={{ $painelJsVersion }}"></script>
		<script type="text/javascript" src="{{ asset('js/modules/relatorio.js') }}?v={{ $relatorioJsVersion }}"></script>
	@endif
</head>
<body id="minwidth-body">
	<div class="ars-shell" id="ars-shell">
		<aside class="ars-shell__sidebar" id="ars-shell-sidebar">
			<div class="ars-shell__sidebar-header">
				<button type="button" class="ars-shell__sidebar-toggle ars-shell__sidebar-toggle--desktop" id="ars-shell-toggle-desktop" aria-label="{{ __('Toggle navigation') }}">
					<span class="ars-shell__icon ars-shell__icon--menu">{!! $shellIcons['menu'] !!}</span>
				</button>
				<a href="{{ url('index') }}" class="ars-shell__brand">
					<span class="ars-shell__brand-mark">
						<img src="{{ asset('css/images/logo.png') }}" alt="{{ __('ARS - NEO Legal') }}" />
					</span>
					<span class="ars-shell__brand-text">
						<strong>ARS</strong>
						<small>{{ __('ARS - NEO Legal') }}</small>
					</span>
				</a>
			</div>
			<nav class="ars-shell__nav" aria-label="{{ __('Main navigation') }}">
				<div class="ars-shell__nav-group">
					<div class="ars-shell__nav-title">{{ __('Workspace') }}</div>
					@foreach ($navLinks as $link)
						<a href="{{ $link['url'] }}" class="ars-shell__nav-link{{ $link['active'] ? ' is-active' : '' }}">
							<span class="ars-shell__icon ars-shell__nav-icon">{!! $shellIcons[$link['key']] !!}</span>
							<span class="ars-shell__nav-label">{{ $link['label'] }}</span>
						</a>
					@endforeach
				</div>
				@if ($pageData->canAdmin())
					<div class="ars-shell__nav-group">
						<div class="ars-shell__nav-title">{{ __('Administration') }}</div>
						@foreach ($adminLinks as $link)
							<a href="{{ $link['url'] }}" class="ars-shell__nav-link{{ $link['active'] ? ' is-active' : '' }}">
								<span class="ars-shell__icon ars-shell__nav-icon">{!! $shellIcons[$link['key']] !!}</span>
								<span class="ars-shell__nav-label">{{ $link['label'] }}</span>
							</a>
						@endforeach
					</div>
				@endif
			</nav>
		</aside>
		<div class="ars-shell__main">
			<header class="ars-shell__topbar">
				<div class="ars-shell__topbar-left">
					<button type="button" class="ars-shell__sidebar-toggle ars-shell__sidebar-toggle--mobile" id="ars-shell-toggle-mobile" aria-label="{{ __('Toggle navigation') }}">
						<span class="ars-shell__icon ars-shell__icon--menu">{!! $shellIcons['menu'] !!}</span>
					</button>
					<div class="ars-shell__page-meta">
						<div class="ars-shell__page-title">{{ $currentPageTitle }}</div>
						<div class="ars-shell__page-subtitle">{{ $sectionSubtitle }}</div>
					</div>
				</div>
				<div class="ars-shell__topbar-actions">
					<div class="ars-shell__meta-chip">{{ __('Level') }}: {{ e($currentUser->level()) }}</div>
					@if ($pageData->topAction())
						@if ($pageData->topAction()->href() !== '')
							<a href="{{ $pageData->topAction()->href() }}" class="ars-shell__action-button ars-shell__action-button--primary">
								<span class="ars-shell__icon">{!! $shellIcons['action'] !!}</span>
								<span>{{ e($pageData->topAction()->label()) }}</span>
							</a>
						@else
							<button type="button" class="ars-shell__action-button ars-shell__action-button--primary" onclick="{{ $pageData->topAction()->javascript() }}">
								<span class="ars-shell__icon">{!! $shellIcons['action'] !!}</span>
								<span>{{ e($pageData->topAction()->label()) }}</span>
							</button>
						@endif
					@endif
					<button type="button" class="ars-shell__action-button" onclick="window.history.go(-1)">
						<span class="ars-shell__icon">{!! $shellIcons['back'] !!}</span>
						<span>{{ __('Back') }}</span>
					</button>
					<a href="{{ url('logout') }}" class="ars-shell__action-button ars-shell__action-link">
						<span class="ars-shell__icon">{!! $shellIcons['logout'] !!}</span>
						<span>{{ __('Logout') }}</span>
					</a>
				</div>
			</header>
			<main class="ars-shell__content-area">
				<section class="{{ $contentCardClass }}">
					<div class="adminform ars-shell__content">
						{!! $contentHtml !!}
					</div>
				</section>
			</main>
		</div>
	</div>
<form name="form_ars" action="{{ e($entryUrl) }}" method="POST" id="form_ars" class="is-hidden">
	@csrf
	<input type="hidden" name="area_id" id="area_id" value="{{ e($pageData->state()->areaId()) }}" />
	<input type="hidden" name="bank_id" id="bank_id" value="{{ e($pageData->state()->bankId()) }}" />
	<input type="hidden" name="codig_and" id="codig_and" value="" />
	<input type="hidden" name="banco_and" id="banco_and" value="" />
	<input type="hidden" name="codig_lnc" id="codig_lnc" value="" />
	<input type="hidden" name="banco_lnc" id="banco_lnc" value="" />
	<input type="hidden" name="detail_bank_id" id="detail_bank_id" value="" />
	<input type="hidden" name="detail_anda_id" id="detail_anda_id" value="" />
	<input type="hidden" name="detail_month" id="detail_month" value="" />
	<input type="hidden" name="detail_year" id="detail_year" value="" />
	<input type="hidden" name="detail_week" id="detail_week" value="" />
	<input type="hidden" name="detail_region_id" id="detail_region_id" value="" />
</form>
@php
	if (is_file($exportPath)) {
		include $exportPath;
	}
@endphp
<script type="text/javascript">
(function () {
	var root = document.getElementById('ars-shell');
	var desktopToggle = document.getElementById('ars-shell-toggle-desktop');
	var mobileToggle = document.getElementById('ars-shell-toggle-mobile');
	var storageKey = 'ars-shell-collapsed';

	if (!root) {
		return;
	}

	function applyCollapsedState(collapsed) {
		if (collapsed) {
			root.classList.add('is-collapsed');
		} else {
			root.classList.remove('is-collapsed');
		}
	}

	function toggleCollapsedState() {
		var collapsed = !root.classList.contains('is-collapsed');
		applyCollapsedState(collapsed);
		if (window.localStorage) {
			window.localStorage.setItem(storageKey, collapsed ? '1' : '0');
		}
	}

	if (window.localStorage && window.localStorage.getItem(storageKey) === '1') {
		applyCollapsedState(true);
	}

	if (desktopToggle) {
		desktopToggle.addEventListener('click', toggleCollapsedState);
	}

	if (mobileToggle) {
		mobileToggle.addEventListener('click', function () {
			root.classList.toggle('is-mobile-open');
		});
	}

	document.addEventListener('click', function (event) {
		if (window.innerWidth > 960) {
			return;
		}

		if (!root.classList.contains('is-mobile-open')) {
			return;
		}

		if (event.target.closest('.ars-shell__sidebar') || event.target.closest('.ars-shell__sidebar-toggle--mobile')) {
			return;
		}

		root.classList.remove('is-mobile-open');
	});
}());
</script>
</body>
</html>
