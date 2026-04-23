<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br" dir="ltr" >
<head>
	<meta http-equiv='content-type' content='text/html; charset=utf-8' >
	<title>ARS ONLINE</title>
	<link href="css/images/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
	<link rel="stylesheet" href="css/template.css" type="text/css" />
	<link rel="stylesheet" href="css/custom-theme/jquery-ui.css">
	<script type="text/javascript" src="js/jquery-1.8.0.min.js">			</script>
	<script type="text/javascript" src="js/jquery-ui-1.8.23.custom.min.js">	</script>
	<script type="text/javascript" src="js/jquery.meio.mask.js">	 		</script>
	<script type="text/javascript" src="js/default.js">						</script>   	
	<!--[if IE 7]><link href="templates/bluestork/css/ie7.css" rel="stylesheet" type="text/css" /><![endif]-->
</head>
<body id="minwidth-body">
<?php
	header('Cache-Control: no cache'); //no cache 
	session_cache_limiter('private_no_expire'); // works //
	session_cache_limiter('public'); // works too session_start(); 
	date_default_timezone_set('America/Recife');
	error_reporting(0);
		
	//ini_set('display_errors',1);
	//ini_set('display_startup_erros',1);
	//error_reporting(E_ALL);

	ini_set("display_errors",0);
	include("inc/seguranca.php");
	include("inc/functions.php");
	include("inc/somadias.php");
	protegePagina(0);
	
	//parâmetros dos usuários
	$usu_setor   = $_SESSION['usuarioSetor'];
	$usu_Cliente = $_SESSION['usuarioCliente'];
	$usu_nivel   = $_SESSION['usuarioNivel'];
	$usu_id      = $_SESSION['usuarioID'];
	$hid_send = "";
	$hid_area = "";
	$hid_flag = "";
	
	if(isset($_POST['hid_send'])){$hid_send = $_POST['hid_send'];}elseif(isset($_GET['hid_send'])){$hid_send = $_GET['hid_send'];}
	if(isset($_POST['hid_area'])){$hid_area = $_POST['hid_area'];}elseif(isset($_GET['hid_area'])){$hid_area = $_GET['hid_area'];}
	if(isset($_POST['hid_flag'])){$hid_flag = $_POST['hid_flag'];}elseif(isset($_GET['hid_flag'])){$hid_flag = $_GET['hid_flag'];}
	$mesano = $arrMonths[(int) date('m')] . " / ". date('Y');
?>
<form name="form_ars" action="index.php" method="POST" id="form_ars">
	<div class="head_bk" ></div>
	<div class="head_fixed" >
		<div id="border-top" class="h_blue">
			<span class="logo"><img src="css/images/logo.png" alt="Sistema de Petição" /></span>
			<span class="title"><a href="index2.php">ARS Online - NEO Jurídico</a></span>
		</div>
			<div id="header-box">
				<div id="topSpace" ></div>
				<div id="module-status">
					<span class="viewsite"><a href="javascript:EnviarDados('index.php','','','');">In&iacute;cio</a></span>
				<?php
				
				switch($_POST['hid_send']){
					case 8:
						?>
						<span class='newuser'><a href='javascript:fc_edit_usu("","I");'>Novo Usuário</a></span>
						<?php
					break;
					
					case 9:
						?>
						<span class='newsetor'><a href='javascript:fc_edit_setor("","I");'>Novo Setor</a></span>
						<?php					
					break;
					
					case 11:
						?>
						<span class='newsetor'><a href='javascript:fc_edit_cliente("","I");'>Novo Cliente</a></span>
						<?php					
					break;
					
					case 12:
						?>
						<span class='newsetor'><a href='javascript:fc_edit_andamento("","I");'>Novo Andamento</a></span>
						<?php
					break;
					case 14:
						?>
						<span class='newsetor'><a href='javascript:fc_edit_metas("","I");'>Nova Meta</a></span>
						<?php
					break;
					case 15:
						?>
						<span class='newsetor'><a href='javascript:fc_edit_sem("","I");'>Nova Semana</a></span>
						<?php
					break;
				}
				
				if($usu_nivel=='ADM' || $usu_nivel=='GER'){
					?>
					<span class="relatory"><a href="#" onclick="EnviarDados('index.php','3','','')">Produção</a></span>
					<span class="viewconfig"><a href="#" onclick="EnviarDados('index.php','5','','')">Administrar</a></span>
					<?php
				}
				?>
				<span class="voltar"><a href="javascript:window.history.go(-1)">Voltar</a></span>
				<span class="logout"><a href="inc/sair.php">Sair</a></span>
			</div>
			<div class="clr"></div>
		</div>
	</div>
<style>
.ui-datepicker-calendar {
	display: none;
}
#obg_date{
	float:left;
	position:absolute;
	color:red;
	margin-left: 86px;
	display:none
}
</style>
	<div id="content-box">
		<div id="element-box">
			<div class="m wbg">
				<div class="adminform">
					<?php
						switch($_POST['hid_send']){
							case 1:
							?>
							<div class="content_body">
								<div class="cpanel-left">
									<div class="cpanel">
									<label><h2>Carteira</h2></label>
									<label for="startDate">Mês / Ano:</label>
									<input type="text"   name="startDate"  id="startDate"  class="date-picker" readonly="readonly" value="<?php echo $mesano; ?>"/>
									<span id="obg_date"></span>
									<input type="hidden" name="mes" id="mes" class="date-picker" value="<?php echo date('m'); ?>"/>
									<input type="hidden" name="ano" id="ano" class="date-picker" value="<?php echo date('Y'); ?>"/>				
									<br>
									<br>
									<br>
									<?php 
										
										$Scli  = " SELECT * ";
										$Scli .= " FROM bancos ";
										$Scli .= " where banco_area=" . $hid_area;
										if($usu_Cliente!=0){
											$Scli .= " and banco_id in (".$usu_Cliente.")";
										}
										$Scli .= " and banco_status in ('Y','P')";
										$Scli .= " order by banco_area";
										$q = mysqli_query($conexao4,$Scli);
										while($w = mysqli_fetch_array($q)){
											echo "<div class='icon-wrapper' style='height:100%'>
													<div class='icon'>";
													echo "<a href='#' onclick='EnviarDados(\"index.php\",2,\"" . $hid_area . "\",\"" . $w['banco_id'] . "\")' class='clspet' grupo='0'>";
													echo "<img src='css/images/header/icon-48-module.png' alt=''  />";
													echo "<span style='position:relative;margin-top:0px'> &nbsp; " . $w['banco_name'] . ($w['banco_class']?" (" . $w['banco_class'] . ")":"") ." &nbsp; </span>";
													echo "</a>
													</div>
												</div>";
										}
									?>	
									</div>
								</div>
							</div>
							<?php
							break;
							case 2:
								echo "<br><br><br><br><br>";
								include "index_ajax.php";
							break;
							case 3:
								?>
								<div class="content_body">
									<div class="cpanel-left">
										<div class="cpanel">
										<label><h2>Produção</h2></label>
										<label for="startSetor">Setor:</label>
										<select name="startSetor" id="startSetor" class="input-default" style="height:20px;width:200px;">
											<option value="">Todas os Setores</option>
											<?php
												$qA = mysqli_query($conexao4,"SELECT * FROM area where area_status = 'Y' " . ($usu_nivel=="ADM"?"":"and area_id='".$usu_setor."'") . " order by area_nome");
												while($wA = mysqli_fetch_array($qA)){
													echo "<option value='".$wA['area_id']."'>". $wA['area_nome'] ."</option>";
												}
											?>
										</select>									
										<label for="startDate">&nbsp;Mês / Ano:</label>
										<input type="text"   name="startDate"  id="startDate"  class="date-picker" readonly="readonly" value="<?php echo $mesano; ?>"/>
										<span id="obg_date"></span>
										<input type="hidden" name="mes" id="mes" class="date-picker" value="<?php echo date('m'); ?>"/>
										<input type="hidden" name="ano" id="ano" class="date-picker" value="<?php echo date('Y'); ?>"/>				
										<br>
										<br>
										<br>
										<br>
										<br>
											<div class="icon-wrapper">
												<div class="icon">
													<a href="#" id="frm" onclick="EnviarDados('index.php',4,'')">
														<img src="css/images/header/icon-48-themes.png" alt=""  /><span>Relatório</span>
													</a>
												</div>
											</div>			
										</div>
									</div>	
								</div>	
								<?php
							break;
							case 4:
								echo "<br><br><br><br><br>";
								if($_POST['geral']==1){
									include "geral_ajax_1.php";
								}else{
									include "geral_ajax.php";
								}
							break;
							case 5:
								include 'admin.php';
							break;
							case 8:
								include 'usu.php';
							break;
							case 9:
								include "setor.php";
							break;
							case 11:
								include "clientes.php";
							break;
							case 12:
								include "andamentos.php";
							break;
							case 13:
								?>
								<div class="content_body">
									<div class="cpanel-left">
										<div class="cpanel">
										<label><h2>Administrar Metas</h2></label>
										<label for="startBanco">Banco:</label>
										<select name="startBanco" id="startBanco" class="input-default" style="height:20px;width:200px;">
										<option></option>
										<?php
										$sB  = " SELECT * FROM bancos";
										$sB .= " where banco_status = 'Y'";
										if($usu_setor!=0){
											$sB .= " and banco_area in (".$usu_setor.")";
										}
										if($usu_Cliente!=0){
											$sB .= " and banco_id in (".$usu_Cliente.")";
										}
										$sB .= " order by banco_cod";
										$qB = mysqli_query($conexao4,$sB);
										while($wB = mysqli_fetch_array($qB)){
											echo "<option value='".$wB['banco_id']."'>". $wB['banco_name']." (".$wB['banco_class'].")</option>";
										}
										?>
										</select>
										<label for="startDate">Mês / Ano:</label>
										<input type="text"   name="startDate"  id="startDate" class="date-picker" readonly="readonly" value="<?php echo $mesano; ?>" style="font-family:Tahoma"/>
										<span id="obg_date"></span>
										<input type="hidden" name="mes" id="mes" value="<?php echo date('m'); ?>"/>
										<input type="hidden" name="ano" id="ano" value="<?php echo date('Y'); ?>"/>				
										<br>
										<br>
										<br>
										<br>
										<br>
											<div class="icon-wrapper">
												<div class="icon">
													<a href="#" id="frm" onclick="EnviarDados('index.php',14,'')">
														<img src="css/images/header/icon-48-themes.png" alt=""  /><span>Metas</span>
													</a>
												</div>
											</div>			
										</div>
									</div>	
								</div>	
								<?php
							break;
							case 14:
								include "metas.php";
							break;
							case 15:
								include "semanas.php";
							break;
							default:
								?>
								<div class="content_body">
									<div class="cpanel-left">
										<div class="cpanel">
										<label><h2>Setores</h2></label>
											<?php 
											$Sarea  = " SELECT * ";
											$Sarea .= " FROM area ";
											$Sarea .= " where area_status = 'Y' ";
											if($usu_setor!=0){
												$Sarea .= " and area_id = '$usu_setor' ";
											}
											$Sarea .= " order by area_nome";
											$q = mysqli_query($conexao4,$Sarea);
											while($w = mysqli_fetch_array($q)){	
												echo  "<div class='icon-wrapper'>
															<div class='icon'>";
															echo "<a href='#' onclick='EnviarDados(\"index.php\",1,\"" . $w['area_id'] . "\",\"\")' class='clspet' grupo='0'>";
															echo "<img src='css/images/header/icon-48-section.png' alt=''  />";
															echo "<span style='position:relative;margin-top:0px'> &nbsp; " . $w['area_nome'] . " &nbsp; </span>";
															echo "</a>
															</div>
														</div>";
											}
											?>
										</div>
									</div>
								</div>	
								<?php
							break;
						}
					?>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="hid_send" id="hid_send" value="<?php echo $hid_send; ?>" />
	<input type="hidden" name="hid_area" id="hid_area" value="<?php echo $hid_area; ?>" />
	<input type="hidden" name="hid_flag" id="hid_flag" value="<?php echo $hid_flag; ?>" />
	<!--div id="footer">
		<p class="copyright" style="color:#ccc">@Torres.</p>
	</div-->
</form>
<?php 

include("../php2/exportar.php");

?>
</body>
</html>