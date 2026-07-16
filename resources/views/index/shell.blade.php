@php
	$defaultJsVersion = is_file(base_path('js/default.js')) ? filemtime(base_path('js/default.js')) : time();
	$setoresJsVersion = is_file(base_path('js/modules/setores.js')) ? filemtime(base_path('js/modules/setores.js')) : time();
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
	<script type="text/javascript" src="js/default.js?v={{ $defaultJsVersion }}"></script>
	<script type="text/javascript" src="js/modules/setores.js?v={{ $setoresJsVersion }}"></script>
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
				@if (!empty($pageData['topAction']))
					<span class="{{ $pageData['topAction']['class'] }}"><a href="javascript:{{ $pageData['topAction']['js'] }}">{{ e($pageData['topAction']['label']) }}</a></span>
				@endif
				@if ($pageData['canAdmin'])
					<span class="relatory"><a href="{{ url('producao') }}">Produ&ccedil;&atilde;o</a></span>
					<span class="viewconfig"><a href="{{ url('admin') }}">Administrar</a></span>
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
				<div class="adminform">
					{!! $contentHtml !!}
				</div>
			</div>
		</div>
	</div>
<form name="form_ars" action="{{ e($entryUrl) }}" method="POST" id="form_ars" style="display:none">
	@csrf
	<input type="hidden" name="area_id" id="area_id" value="{{ e((string) ($pageData['state']['area_id'] ?? '')) }}" />
	<input type="hidden" name="bank_id" id="bank_id" value="{{ e((string) ($pageData['state']['bank_id'] ?? '')) }}" />
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
