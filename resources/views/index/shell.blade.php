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
	$entryUrl = isset($entryUrl) ? (string) $entryUrl : url('index');
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br" dir="ltr">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>ARS ONLINE</title>
	<link href="css/images/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
	<link rel="stylesheet" href="css/template.css" type="text/css" />
	<link rel="stylesheet" href="css/custom-theme/jquery-ui.css">
	<script type="text/javascript" src="js/jquery-1.8.0.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.23.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery.meio.mask.js"></script>
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
</head>
<body id="minwidth-body">
	<div class="head_bk"></div>
	<div class="head_fixed">
		<div id="border-top" class="h_blue">
			<span class="logo"><img src="css/images/logo.png" alt="Sistema de Peti&ccedil;&atilde;o" /></span>
			<span class="title"><a href="{{ url('index') }}">ARS Online - NEO Jur&iacute;dico</a></span>
		</div>
		<div id="header-box">
			<div id="topSpace"></div>
			<div id="module-status">
				<span class="viewsite"><a href="{{ url('index') }}">In&iacute;cio</a></span>
				@if ($pageData->topAction())
					<span class="{{ $pageData->topAction()->className() }}"><a href="javascript:{{ $pageData->topAction()->javascript() }}">{{ e($pageData->topAction()->label()) }}</a></span>
				@endif
				@if ($pageData->canAdmin())
					<span class="relatory"><a href="{{ url('producao') }}">Produ&ccedil;&atilde;o</a></span>
					<span class="viewconfig"><a href="{{ url('***REMOVED***') }}">Administrar</a></span>
				@endif
				<span class="voltar"><a href="javascript:window.history.go(-1)">Voltar</a></span>
				<span class="logout"><a href="{{ url('logout') }}">Sair</a></span>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<style>
	.ui-datepicker { display: none; }
	.ui-datepicker-calendar { display: none; }
	#obg_date { float:left; position:absolute; color:red; margin-left:86px; display:none }
	</style>
	<div id="content-box">
		<div id="element-box">
			<div class="m wbg">
				<div class="***REMOVED***form">
					{!! $contentHtml !!}
				</div>
			</div>
		</div>
	</div>
<form name="form_ars" action="{{ e($entryUrl) }}" method="POST" id="form_ars" style="display:none">
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
