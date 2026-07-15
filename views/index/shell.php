<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br" dir="ltr">
<head>
	<meta http-equiv='content-type' content='text/html; charset=utf-8'>
	<title>ARS ONLINE</title>
	<link href="css/images/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
	<link rel="stylesheet" href="css/template.css" type="text/css" />
	<link rel="stylesheet" href="css/custom-theme/jquery-ui.css">
	<script type="text/javascript" src="js/jquery-1.8.0.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.23.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery.meio.mask.js"></script>
	<?php $defaultJsVersion = is_file(base_path('js/default.js')) ? filemtime(base_path('js/default.js')) : time(); ?>
	<script type="text/javascript" src="js/default.js?v=<?php echo $defaultJsVersion; ?>"></script>
</head>
<body id="minwidth-body">
<?php $entryUrl = isset($entryUrl) ? (string) $entryUrl : 'index.php'; ?>
<form name="form_ars" action="<?php echo htmlspecialchars($entryUrl, ENT_QUOTES, 'UTF-8'); ?>" method="POST" id="form_ars">
	<div class="head_bk"></div>
	<div class="head_fixed">
		<div id="border-top" class="h_blue">
			<span class="logo"><img src="css/images/logo.png" alt="Sistema Ars" /></span>
			<span class="title"><a href="<?php echo htmlspecialchars($entryUrl, ENT_QUOTES, 'UTF-8'); ?>">ARS Online - NEO Jurídico</a></span>
		</div>
		<div id="header-box">
			<div id="topSpace"></div>
			<div id="module-status">
				<span class="viewsite"><a href="index.php">In&iacute;cio</a></span>
				<?php if (!empty($pageData['topAction'])): ?>
					<span class="<?php echo $pageData['topAction']['class']; ?>"><a href='javascript:<?php echo $pageData["topAction"]["js"]; ?>'><?php echo htmlspecialchars($pageData['topAction']['label'], ENT_QUOTES, 'UTF-8'); ?></a></span>
				<?php endif; ?>
				<?php if ($pageData['canAdmin']): ?>
					<span class="relatory"><a href="producao.php">Produção</a></span>
					<span class="viewconfig"><a href="***REMOVED***.php">Administrar</a></span>
				<?php endif; ?>
				<span class="voltar"><a href="javascript:window.history.go(-1)">Voltar</a></span>
				<span class="logout"><a href="inc/sair.php">Sair</a></span>
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
					<?php echo $contentHtml; ?>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="area_id" id="area_id" value="<?php echo htmlspecialchars((string) (isset($pageData['state']['area_id']) ? $pageData['state']['area_id'] : ''), ENT_QUOTES, 'UTF-8'); ?>" />
	<input type="hidden" name="bank_id" id="bank_id" value="<?php echo htmlspecialchars((string) (isset($pageData['state']['bank_id']) ? $pageData['state']['bank_id'] : ''), ENT_QUOTES, 'UTF-8'); ?>" />
</form>
<?php if (is_file($exportPath)) { include $exportPath; } ?>
</body>
</html>
