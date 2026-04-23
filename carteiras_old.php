<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br" dir="ltr" >
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>Controle da ARS</title>
<script type='text/javascript' src="../js/jquery-1.9.1.js"></script>
</head>
<?php

error_reporting(0);
ini_set("display_errors", 0 );
//echo ">>>>>>>>><br><br><br><br>".$_POST['hid_enviar']."<<<<<<<<<";
include("inc/seguranca.php");

include('../php2/functions.php');
include('camp_dados.php');
protegePagina();

$mes = $_GET['mes']?$_GET['mes']:date('m');
$ano = $_GET['ano']?$_GET['ano']:date('Y');

$sel = "select * from bancos as b ";

$Qsel = mysqli_query($conexao4,$sel);

?>
<style>
.cls_inp{
	width:40px;
}
.cls_sema{
	width:360px;
}
.cls_data{
	width:80px;
}
</style>
<script>
	//function dias_sem(valor1,valor2){
	//	var i=1;
	//	var a=1;
	//	var ss="";
	//	for(a=1;a<=2;a++){
	//		ss = (a==1?'ini':'fim');
	//		for(i=1;i<=4;i++){
	//			//alert(ss + ' - ' + i);
	//			$.ajax({
	//				type: 'POST',
	//				url:  'ajax_semanas.php',
	//				data: 'mes='+valor1+'&ano='+valor2+'&sem='+i+'&par='+ss,
	//				success: function(retorno_ajax){
	//					//alert(retorno_ajax);
	//					alert($("#"+ss+"_"+i).val(retorno_ajax));
	//				}
	//			});
	//		}
	//		i=1;
	//	}
	//	a=0;
	//}
	
</script>
<form action="index_ajax.php" method="post">
<table align="center" height="50%" width="50%" border="1" cellspacing='2' cellpadding='2' style="font-family:arial;font-size:10pt; border-collapse: collapse;">
	<tr><td align="center" colspan="5">ARS - BVAA</tr>
	<tr>
		<td align="center" colspan="5">Banco: 
		<select name="hid_flag" class="cls_sema" >
		<option value=""></option>
		<?php
		while($Wsel = mysqli_fetch_array($Qsel)){
			echo "<option value='".$Wsel['banco_id']."'>".utf8_encode($Wsel['banco_name'])."</option>";
		}
		?>
		</select>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="5">
			Mês: 
			<select name="mes" id="mes" class="cls_data" >
				<?php 
				foreach($arr_mes as $d => $m){
					echo "<option value='$d'>$m</option>";
				}
				?>
			</select>	
			Ano: 
			<select name="ano" id="ano" class="cls_data" >
				<?php 
				echo "<option value='".date('Y')."'>".date('Y')."</option>";
				for($a=2016;$a<=2020;$a++){
					$cur = ($a==date('Y')?"selected":"");
					echo "<option value='$a' $cur>$a</option>";
				}
				?>
			</select>
		<!--button type="button" onclick="dias_sem($('#mes').val(),$('#ano').val());">Preencher</button-->
		</td>
	</tr>
	<!--tr>
		<td align="center" width="6%" class="cls_dados" rowspan="2">Semanas:	</td>
		<td align="center" width="6%" class="cls_dados">1ª Semana	</td>
		<td align="center" width="6%" class="cls_dados">2ª Semana	</td>
		<td align="center" width="6%" class="cls_dados">3ª Semana	</td>
		<td align="center" width="6%" class="cls_dados">4ª Semana	</td>
	</tr>
	<tr>
		<td align="center" width="6%" class="cls_dados"><input type="text" name="pri_ini" id="pri_ini" class="cls_inp" /> à <input type="text" name="pri_fim" id="pri_fim" class="cls_inp" /></td>
		<td align="center" width="6%" class="cls_dados"><input type="text" name="seg_ini" id="seg_ini" class="cls_inp" /> à <input type="text" name="seg_fim" id="seg_fim" class="cls_inp" /></td>
		<td align="center" width="6%" class="cls_dados"><input type="text" name="ter_ini" id="ter_ini" class="cls_inp" /> à <input type="text" name="ter_fim" id="ter_fim" class="cls_inp" /></td>
		<td align="center" width="6%" class="cls_dados"><input type="text" name="qua_ini" id="qua_ini" class="cls_inp" /> à <input type="text" name="qua_fim" id="qua_fim" class="cls_inp" /></td>
	</tr-->
	<tr><td align="center" colspan="5"><input type="submit" value="Enviar" style="cursor:pointer;height:30px;width:100px;"></tr>
</table>
<form>

