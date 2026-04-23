<?php 
	include("seguranca.php");
	protegePagina(0);	

	?>
	<option value="">  </option>
	<?php 
	if($_POST['dados']==0){
		if($_POST['flag']==1){
			$Qand = sqlsrv_query($conexao1,"SELECT a.TipoAndamentoProcesso FROM Andamento_Processo AS a WITH (NOLOCK) GROUP BY a.TipoAndamentoProcesso ORDER BY a.TipoAndamentoProcesso ASC");
			while($Wand = sqlsrv_fetch_array($Qand, SQLSRV_FETCH_ASSOC)){
				?>
				<option value="<?php echo utf8_encode($Wand['TipoAndamentoProcesso']); ?>"> <?php echo utf8_encode($Wand['TipoAndamentoProcesso']); ?></option>
				<?php 
			}
		}elseif($_POST['flag']==2){
			$Qlan = sqlsrv_query($conexao1,"SELECT l.TipoLancamento FROM Lancamento_Processo AS l WITH (NOLOCK) WHERE l.ClassificaoLancamento='".utf8_decode('Honorário')."' GROUP BY l.TipoLancamento ORDER BY l.TipoLancamento ASC");
			while($Wlan = sqlsrv_fetch_array($Qlan, SQLSRV_FETCH_ASSOC)){
				?>
				<option value="<?php echo utf8_encode($Wlan['TipoLancamento']); ?>"> <?php echo utf8_encode($Wlan['TipoLancamento']); ?></option>
				<?php 
			}
		}
	}elseif($_POST['dados']==1){
		$area_id  = $_POST['flag'];
		$scli  = " SELECT * FROM bancos";
		$scli .= " where banco_area = $area_id";
		$scli .= " order by banco_name";
		$qcli = mysqli_query($conexao4,$scli);
		while($wcli = mysqli_fetch_array($qcli)){
			?>
			<option value="<?php echo $wcli['banco_id']; ?>"> <?php echo utf8_encode($wcli['banco_name']); ?></option>
			<?php 
		}
	}
?>