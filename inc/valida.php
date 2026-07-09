<?php

error_reporting(0);
ini_set('display_errors', 0);

include 'seguranca.php';

$forcePasswordChange = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$usuario = isset($_POST['username']) ? $_POST['username'] : '';
	$senha = isset($_POST['passwd']) ? $_POST['passwd'] : '';

	$authResult = ars_auth_service()->attempt($usuario, $senha);

	if ($authResult !== false) {
		if (ars_auth_service()->requiresPasswordChange($authResult)) {
			$forcePasswordChange = true;
		} else {
			ars_refresh_user_access($authResult['id_usu'], $conexao4);
			if (!headers_sent()) {
				header('Location: ../index.php');
				exit;
			}

			exit('<script>window.location="../index.php";</script>');
		}
	} else {
		expulsaVisitante(1);
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<script type="text/javascript" src="../js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.23.custom.min.js"></script>
<link rel="stylesheet" href="../css/template.css" type="text/css" />
<link rel="stylesheet" href="../css/custom-theme/jquery-ui-1.8.23.custom.css">
<style type="text/css">
#dialog-new-pass table {
	width: 100%;
}

#dialog-new-pass td {
	padding: 4px 6px;
	vertical-align: middle;
}

#dialog-new-pass .cls_usu {
	width: 180px;
}
</style>
<script language="JavaScript">
function new_pass(){
	var tt = "Nova senha";
	$("#dialog-new-pass").dialog({
		title: tt,
		modal: true,
		autoOpen: true,
		height: 240,
		width: 360,
		close: function(){
			location.href="../login.php";
		},
		buttons: {
			Salvar: function() {
				if($("#senha_usu1").val()!=$("#senha_usu2").val()){
					alert("As senhas não conferem!");
				}else if($("#senha_usu1").val()==""){
					alert("Preencha o campo senha!");
				}else{
					$.ajax({
						type: "POST",
						url : "../inc/ajax_newpass.php",
						data: "flag=U" +
							  "&id_usu=" + $("#id_usu").val() +
							  "&senha_usu1=" + $("#senha_usu1").val(),
						success: function(x){
							$("<div></div>")
							.html("<br><table align='center'><tr><td>Senha alterada com sucesso!</td></tr></table>")
							.dialog({
								modal: true,
								autoOpen: true,
								buttons: {
									"Fechar": function() {
										$(this).dialog("close");
										location.href="../index.php";
									}
								},
								title: "Alerta"
							});
						}
					});
				}
			},
			Sair: function() {
				$(this).dialog("close");
			}
		}
	});
}
</script>
<?php if ($forcePasswordChange): ?>
<script>
$(function() { new_pass(); });
</script>
<?php endif; ?>
<div id="dialog-new-pass" title="Nova senha" style="display:none; text-align:left;">
	<p class="validateTips">Alteração de senha obrigatória!</p>
	<fieldset>
		<div>
			<table>
				<tr>
					<td><label>Nova senha</label></td>
					<td><input type="password" class="cls_usu" name="senha_usu1" id="senha_usu1" value="" /></td>
				</tr>
				<tr>
					<td><label>Repete a senha</label></td>
					<td><input type="password" class="cls_usu" name="senha_usu2" id="senha_usu2" value="" /></td>
				</tr>
			</table>
			<input type="hidden" class="cls_usu" name="id_usu" id="id_usu" value="<?php echo isset($_SESSION['usuarioID']) ? $_SESSION['usuarioID'] : ''; ?>" />
		</div>
	</fieldset>
</div>
