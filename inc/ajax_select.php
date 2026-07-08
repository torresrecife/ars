<?php 
	include("seguranca.php");
	protegePagina(0);	
	header("Content-Type: text/html; charset=UTF-8", true);
	$conexao1 = ars_sqlsrv_connection();

	?>
	<option value="">  </option>
	<?php 
	if($_POST['dados']==0){
		if($_POST['flag']==1){
			$Qand = sqlsrv_query($conexao1,"SELECT t.TIAP_Descricao FROM Tipo_Andamento_Processo AS t WITH (NOLOCK) WHERE t.TIAP_Descricao IS NOT NULL AND ISNULL(t.TIAP_Inativo, 0) = 0 AND ISNULL(t.TIAP_Excluido, 0) = 0 GROUP BY t.TIAP_Descricao ORDER BY t.TIAP_Descricao ASC");
			while($Wand = sqlsrv_fetch_array($Qand, SQLSRV_FETCH_ASSOC)){
				?>
				<option value="<?php echo htmlspecialchars($Wand['TIAP_Descricao'], ENT_QUOTES, 'UTF-8'); ?>"> <?php echo htmlspecialchars($Wand['TIAP_Descricao'], ENT_QUOTES, 'UTF-8'); ?></option>
				<?php 
			}
		}elseif($_POST['flag']==2){
			$Qlan = sqlsrv_query($conexao1,"SELECT l.TipoLancamento FROM v_Lancamento_Processo AS l WITH (NOLOCK) WHERE l.ClassicaoLancamento LIKE 'Honor%' AND l.TipoLancamento IS NOT NULL GROUP BY l.TipoLancamento ORDER BY l.TipoLancamento ASC");
			while($Wlan = sqlsrv_fetch_array($Qlan, SQLSRV_FETCH_ASSOC)){
				?>
				<option value="<?php echo htmlspecialchars($Wlan['TipoLancamento'], ENT_QUOTES, 'UTF-8'); ?>"> <?php echo htmlspecialchars($Wlan['TipoLancamento'], ENT_QUOTES, 'UTF-8'); ?></option>
				<?php 
			}
		}
	}elseif($_POST['dados']==1){
		$area_id  = (int) $_POST['flag'];
		$scli  = " SELECT * FROM bancos";
		if($area_id > 0){
			$scli .= " where banco_area = $area_id";
		}
		$scli .= " order by banco_name";
		$qcli = mysqli_query($conexao4,$scli);
		while($wcli = mysqli_fetch_array($qcli)){
			?>
			<option value="<?php echo $wcli['banco_id']; ?>"> <?php echo htmlspecialchars($wcli['banco_name'], ENT_QUOTES, 'UTF-8'); ?></option>
			<?php 
		}
	}
?>
