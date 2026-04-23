<label><h2>Carteira(s)</h2></label>
<?php 
$s  = " SELECT * FROM bancos";
$s .= " where banco_status = 'Y'";
if($usu_setor!=0){
	$s .= " and banco_area in (".$usu_setor.")";
}
if($usu_Cliente!=0){
	$s .= " and banco_id in (".$usu_Cliente.")";
}
$s .= " order by banco_area";
$q = mysqli_query($conexao4,$s);
while($w = mysqli_fetch_array($q)){	
	echo "<div class='icon-wrapper' style='height:100%'>
				<div class='icon'>";
				echo "<a href='#' onclick='EnviarDados(\"index.php\",2,\"" . $hid_area . "\",\"" . $w['banco_id'] . "\")' class='clspet' grupo='0'>";
				echo "<img src='css/images/header/icon-48-module.png' alt=''  />";
				echo "<span style='position:relative;margin-top:0px'> &nbsp; " . $w['banco_name'] . " &nbsp; </span>";
				echo "</a>
				</div>
			</div>";
}
?>	