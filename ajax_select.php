<?php

require __DIR__ . '/inc/seguranca.php';

protegePagina(0);

header('Content-Type: text/html; charset=UTF-8', true);

$conexao1 = ars_sqlsrv_connection();

?>
<option value="">  </option>
<?php
if ((string) (isset($_POST['dados']) ? $_POST['dados'] : '') === '0') {
	if ((string) (isset($_POST['flag']) ? $_POST['flag'] : '') === '1') {
		$query = sqlsrv_query($conexao1, "SELECT t.TIAP_Descricao FROM Tipo_Andamento_Processo AS t WITH (NOLOCK) WHERE t.TIAP_Descricao IS NOT NULL AND ISNULL(t.TIAP_Inativo, 0) = 0 AND ISNULL(t.TIAP_Excluido, 0) = 0 GROUP BY t.TIAP_Descricao ORDER BY t.TIAP_Descricao ASC");
		while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
			?>
			<option value="<?php echo htmlspecialchars($row['TIAP_Descricao'], ENT_QUOTES, 'UTF-8'); ?>"> <?php echo htmlspecialchars($row['TIAP_Descricao'], ENT_QUOTES, 'UTF-8'); ?></option>
			<?php
		}
	} elseif ((string) (isset($_POST['flag']) ? $_POST['flag'] : '') === '2') {
		$query = sqlsrv_query($conexao1, "SELECT l.TipoLancamento FROM v_Lancamento_Processo AS l WITH (NOLOCK) WHERE l.ClassicaoLancamento LIKE 'Honor%' AND l.TipoLancamento IS NOT NULL GROUP BY l.TipoLancamento ORDER BY l.TipoLancamento ASC");
		while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
			?>
			<option value="<?php echo htmlspecialchars($row['TipoLancamento'], ENT_QUOTES, 'UTF-8'); ?>"> <?php echo htmlspecialchars($row['TipoLancamento'], ENT_QUOTES, 'UTF-8'); ?></option>
			<?php
		}
	}
} elseif ((string) (isset($_POST['dados']) ? $_POST['dados'] : '') === '1') {
	$areaId = isset($_POST['flag']) ? (int) $_POST['flag'] : 0;
	$sql = "SELECT * FROM bancos";
	if ($areaId > 0) {
		$sql .= " WHERE banco_area = " . $areaId;
	}
	$sql .= " ORDER BY banco_name";
	$query = mysqli_query($conexao4, $sql);
	while ($row = mysqli_fetch_array($query)) {
		?>
		<option value="<?php echo $row['banco_id']; ?>"> <?php echo htmlspecialchars($row['banco_name'], ENT_QUOTES, 'UTF-8'); ?></option>
		<?php
	}
}
