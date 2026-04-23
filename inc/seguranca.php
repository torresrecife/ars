<?php

session_start();

$_SG['conectaServidor'] = true;
$_SG['caseSensitive'] 	= false;
$_SG['validaSempre'] 	= true;
$_SG['servidor'] 		= '10.81.11.202';
$_SG['usuario'] 		= 'admin';
$_SG['senha'] 			= 'bvaa@2025!';
$_SG['banco'] 			= 'planweb';
$_SG['paginaLogin'] 	= 'index.php';
$_SG['tabela'] 			= 'usuarios';

if ($_SG['conectaServidor'] == true) {
	$conexao4 = mysqli_connect($_SG['servidor'], $_SG['usuario'], $_SG['senha'],$_SG['banco']) or die("MySQL: Não foi possível conectar-se ao servidor [".$_SG['servidor']."]. test");
}
//////////aguarda a atualização do NEO Jurídico///////////////////
if(date("i")>=00 && date("i")<03){
	echo "<meta http-equiv='refresh' content='30'>";
	echo "<div align='center' style='margin-top:50px; height:50px;font-size:18pt'>";
	echo "Aguarde a atualização do sistema. Tente novamente em 2 minutos";
	echo "</div>";
	exit;
}
//$serverName = "201.48.211.212";
//$serverName = "187.72.30.138";
//$serverName = "10.81.11.102";
$serverName = "172.31.20.102";
//$serverName = "187.115.147.154";
//$connectionInfo = array("UID" => "qlik", "PWD" => "bv@@jur2018", "Database"=>"Neo_Juridico_Espelho");
$connectionInfo = array("UID" => "qlik", "PWD" => "bv@@jur2018", "Database"=>"Neo_Juridico_replica");
$conexao1 = sqlsrv_connect( $serverName, $connectionInfo);

if( $conexao1 ){
     //echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br/>";
     die( print_r( sqlsrv_errors(), true));
}

function validaUsuario($usuario, $senha, $conex){
	global $_SG;
	$cS 		= ($_SG['caseSensitive']) ? 'BINARY' : '';
	$nusuario 	= addslashes($usuario);
	$nsenha   	= addslashes($senha);
	$sql 	  	= "SELECT * FROM `".$_SG['tabela']."` WHERE " . $cS . " `login_usu` = '" . $nusuario . "' AND ".$cS." `senha_usu` = '".$nsenha."' LIMIT 1";
	$query 	    = mysqli_query($conex,$sql);
	$resultado 	= mysqli_fetch_assoc($query);
	
	if (empty($resultado)){
		return false;
	}else{
		$_SESSION['usuarioID'] 	  	= $resultado['id_usu']; 
		$_SESSION['usuarioNome']  	= $resultado['nome_usu']; 
		$_SESSION['usuarioNivel'] 	= $resultado['nivel_usu'];
		$_SESSION['usuarioST'] 	  	= $resultado['status_usu'];
		$_SESSION['usuarioSetor'] 	= $resultado['id_setor'];
		$_SESSION['usuarioCliente'] = $resultado['id_cliente'];
		
		if ($_SG['validaSempre'] == true){
			$_SESSION['usuarioLogin'] = $usuario;
			$_SESSION['usuarioSenha'] = $senha;
		}
		return true;
	}
}
function protegePagina($valor){
	global $_SG;
	if (!isset($_SESSION['usuarioID']) OR !isset($_SESSION['usuarioNome'])){
		expulsaVisitante($valor);
	}elseif(!isset($_SESSION['usuarioID']) OR !isset($_SESSION['usuarioNome'])) {
		if ($_SG['validaSempre'] == true){
			if (!validaUsuario($_SESSION['usuarioLogin'], $_SESSION['usuarioSenha'])){
				expulsaVisitante($valor);
			}
		}
	}
}
function expulsaVisitante($valor) {
	global $_SG;
	unset($_SESSION['usuarioID'], $_SESSION['usuarioNome'], $_SESSION['usuarioLogin'], $_SESSION['usuarioSenha']);
	$host=$_SERVER['HTTP_HOST'];
	if($valor==1){
		exit ('<SCRIPT LANGUAGE="JavaScript">window.location="http://'.$host.'/ars/login.php?alerta=1"</script>');
	}else{
		exit ('<SCRIPT LANGUAGE="JavaScript">window.location="http://'.$host.'/ars/login.php"</script>');
	}
}
?>