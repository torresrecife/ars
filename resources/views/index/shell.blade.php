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
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br" dir="ltr">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ __('ARS Control') }}</title>
	<link href="css/images/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
	<link rel="stylesheet" href="css/template.css" type="text/css" />
	<link rel="stylesheet" href="css/custom-theme/jquery-ui.css">
	@if (is_file(public_path('mix-manifest.json')) && is_file(public_path('build/css/app.css')))
		<link rel="stylesheet" href="{{ asset('public/' . ltrim(mix('/build/css/app.css'), '/')) }}">
	@else
		<link rel="stylesheet" href="{{ asset('css/ars-modern.css') }}">
	@endif
	<script type="text/javascript" src="js/jquery-1.8.0.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.23.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery.meio.mask.js"></script>
	<script type="text/javascript">
	window.arsTranslations = {!! json_encode($arsTranslations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
	</script>
	@if ($compiledModulesAvailable)
		<script type="text/javascript" src="{{ asset('public/' . ltrim(mix('/build/js/ars-modules.js'), '/')) }}"></script>
	@else
		<script type="text/javascript" src="js/modules/helpers.js?v={{ $helpersJsVersion }}"></script>
		<script type="text/javascript" src="js/modules/setores.js?v={{ $setoresJsVersion }}"></script>
		<script type="text/javascript" src="js/modules/usuarios.js?v={{ $usuariosJsVersion }}"></script>
		<script type="text/javascript" src="js/modules/semanas.js?v={{ $semanasJsVersion }}"></script>
		<script type="text/javascript" src="js/modules/regioes.js?v={{ $regioesJsVersion }}"></script>
		<script type="text/javascript" src="js/modules/clientes.js?v={{ $clientesJsVersion }}"></script>
		<script type="text/javascript" src="js/modules/andamentos.js?v={{ $andamentosJsVersion }}"></script>
		<script type="text/javascript" src="js/modules/metas.js?v={{ $metasJsVersion }}"></script>
		<script type="text/javascript" src="js/modules/painel.js?v={{ $painelJsVersion }}"></script>
		<script type="text/javascript" src="js/modules/relatorio.js?v={{ $relatorioJsVersion }}"></script>
	@endif
</head>
<body id="minwidth-body">
	<div class="head_bk"></div>
	<div class="head_fixed">
		<div id="border-top" class="h_blue">
			<span class="logo"><img src="css/images/logo.png" alt="{{ __('Petition System') }}" /></span>
			<span class="title"><a href="{{ url('index') }}">{{ __('ARS - NEO Legal') }}</a></span>
		</div>
		<div id="header-box">
			<div id="topSpace"></div>
			<div id="module-status">
				<span class="viewsite"><a href="{{ url('index') }}">{{ __('Home') }}</a></span>
				@if ($pageData->topAction())
					<span class="{{ $pageData->topAction()->className() }}"><a href="javascript:{{ $pageData->topAction()->javascript() }}">{{ e($pageData->topAction()->label()) }}</a></span>
				@endif
				@if ($pageData->canAdmin())
					<span class="relatory"><a href="{{ url('producao') }}">{{ __('Production') }}</a></span>
					<span class="viewconfig"><a href="{{ url('***REMOVED***') }}">{{ __('Admin') }}</a></span>
				@endif
				<span class="voltar"><a href="javascript:window.history.go(-1)">{{ __('Back') }}</a></span>
				<span class="logout"><a href="{{ url('logout') }}">{{ __('Logout') }}</a></span>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<div id="content-box">
		<div id="element-box">
			<div class="m wbg">
				<div class="***REMOVED***form">
					{!! $contentHtml !!}
				</div>
			</div>
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
</body>
</html>
