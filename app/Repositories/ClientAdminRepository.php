<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

class ClientAdminRepository
{
	/** @var mysqli */
	private $connection;

	/** @var resource|null */
	private $sqlsrvConnection;

	public function __construct(mysqli $connection, $sqlsrvConnection = null)
	{
		$this->connection = $connection;
		$this->sqlsrvConnection = $sqlsrvConnection;
	}

	public function all()
	{
		$sql = "SELECT b.*, a.area_nome, DATE_FORMAT(b.banco_creator, '%d/%m/%Y') AS datacad
			FROM bancos AS b
			JOIN area AS a ON a.area_id = b.banco_area
			GROUP BY b.banco_id
			ORDER BY b.banco_id";
		$result = mysqli_query($this->connection, $sql);
		if (!$result) {
			return array();
		}

		$rows = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function listDadosByBanco()
	{
		$result = mysqli_query($this->connection, "SELECT banco_id, dados_cod FROM dados ORDER BY banco_id, dados_id");
		if (!$result) {
			return array();
		}

		$rows = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function listDadosByBancoId($bancoId)
	{
		$stmt = mysqli_prepare($this->connection, "SELECT dados_cod FROM dados WHERE banco_id = ? ORDER BY dados_id");
		if (!$stmt) {
			return array();
		}

		$bancoId = (int) $bancoId;
		mysqli_stmt_bind_param($stmt, 'i', $bancoId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);

		$rows = array();
		while ($result && ($row = mysqli_fetch_assoc($result))) {
			$rows[] = $row;
		}

		mysqli_stmt_close($stmt);

		return $rows;
	}

	public function findById($id)
	{
		$stmt = mysqli_prepare($this->connection, "SELECT * FROM bancos WHERE banco_id = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		$id = (int) $id;
		mysqli_stmt_bind_param($stmt, 'i', $id);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row;
	}

	public function listAreas()
	{
		$result = mysqli_query($this->connection, "SELECT * FROM area ORDER BY area_id");
		if (!$result) {
			return array();
		}

		$rows = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function listCarteiras()
	{
		if (!$this->sqlsrvConnection || !function_exists('sqlsrv_query')) {
			return array();
		}

		$sql = "SELECT c.CART_Descricao AS Carteira
			FROM Carteira AS c WITH (NOLOCK)
			WHERE c.CART_Descricao IS NOT NULL
			  AND LTRIM(RTRIM(c.CART_Descricao)) <> ''
			GROUP BY c.CART_Descricao
			ORDER BY c.CART_Descricao";
		$query = sqlsrv_query($this->sqlsrvConnection, $sql);
		if ($query === false) {
			return array();
		}

		$rows = array();
		while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function insertBank(array $data)
	{
		$sql = "INSERT INTO bancos (
			banco_name, banco_cod, banco_creator, banco_area,
			banco_status, banco_class, simulador, banco_curto
		) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		$name = (string) $data['banco_name'];
		$cod = (string) $data['banco_cod'];
		$creator = (string) $data['banco_creator'];
		$area = (int) $data['banco_area'];
		$status = (string) $data['banco_status'];
		$class = (string) $data['banco_class'];
		$simulador = (int) $data['simulador'];
		$curto = (string) $data['banco_curto'];

		mysqli_stmt_bind_param($stmt, 'sssissis', $name, $cod, $creator, $area, $status, $class, $simulador, $curto);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	public function updateBank(array $data)
	{
		$sql = "UPDATE bancos SET
			banco_name = ?,
			banco_cod = ?,
			banco_creator = ?,
			banco_area = ?,
			banco_status = ?,
			banco_class = ?,
			simulador = ?,
			banco_curto = ?
			WHERE banco_id = ?";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		$name = (string) $data['banco_name'];
		$cod = (string) $data['banco_cod'];
		$creator = (string) $data['banco_creator'];
		$area = (int) $data['banco_area'];
		$status = (string) $data['banco_status'];
		$class = (string) $data['banco_class'];
		$simulador = (int) $data['simulador'];
		$curto = (string) $data['banco_curto'];
		$id = (int) $data['banco_id'];

		mysqli_stmt_bind_param($stmt, 'sssissisi', $name, $cod, $creator, $area, $status, $class, $simulador, $curto, $id);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	public function getLastInsertId()
	{
		return (int) mysqli_insert_id($this->connection);
	}

	public function createCarteira($bancoId, $date)
	{
		$sql = "INSERT INTO carteira (
			banco_id, carteira_condicao, carteira_cod, carteira_vinc, carteira_date, carteira_status
		) VALUES (?, 'Carteira', '1', 'IN', ?, 'Y')";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		$bancoId = (int) $bancoId;
		$date = (string) $date;
		mysqli_stmt_bind_param($stmt, 'is', $bancoId, $date);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	public function findCarteiraIdByBanco($bancoId)
	{
		$stmt = mysqli_prepare($this->connection, "SELECT carteira_id FROM carteira WHERE banco_id = ? ORDER BY carteira_id DESC LIMIT 1");
		if (!$stmt) {
			return 0;
		}

		$bancoId = (int) $bancoId;
		mysqli_stmt_bind_param($stmt, 'i', $bancoId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row ? (int) $row['carteira_id'] : 0;
	}

	public function deleteDadosByBanco($bancoId)
	{
		$stmt = mysqli_prepare($this->connection, "DELETE FROM dados WHERE banco_id = ?");
		if (!$stmt) {
			return false;
		}

		$bancoId = (int) $bancoId;
		mysqli_stmt_bind_param($stmt, 'i', $bancoId);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	public function insertDados($carteiraId, $bancoId, $dadosCod, $date)
	{
		$sql = "INSERT INTO dados (carteira_id, banco_id, dados_cod, dados_date, dados_status)
			VALUES (?, ?, ?, ?, 'Y')";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		$carteiraId = (int) $carteiraId;
		$bancoId = (int) $bancoId;
		$dadosCod = (string) $dadosCod;
		$date = (string) $date;
		mysqli_stmt_bind_param($stmt, 'iiss', $carteiraId, $bancoId, $dadosCod, $date);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	public function deleteBankCascade($bancoId)
	{
		$bancoId = (int) $bancoId;
		$ok = true;

		$stmtDados = mysqli_prepare($this->connection, "DELETE FROM dados WHERE banco_id = ?");
		$stmtCarteira = mysqli_prepare($this->connection, "DELETE FROM carteira WHERE banco_id = ? LIMIT 1");
		$stmtBanco = mysqli_prepare($this->connection, "DELETE FROM bancos WHERE banco_id = ? LIMIT 1");

		if (!$stmtDados || !$stmtCarteira || !$stmtBanco) {
			if ($stmtDados) {
				mysqli_stmt_close($stmtDados);
			}
			if ($stmtCarteira) {
				mysqli_stmt_close($stmtCarteira);
			}
			if ($stmtBanco) {
				mysqli_stmt_close($stmtBanco);
			}
			return false;
		}

		mysqli_stmt_bind_param($stmtDados, 'i', $bancoId);
		$ok = $ok && mysqli_stmt_execute($stmtDados);
		mysqli_stmt_close($stmtDados);

		mysqli_stmt_bind_param($stmtCarteira, 'i', $bancoId);
		$ok = $ok && mysqli_stmt_execute($stmtCarteira);
		mysqli_stmt_close($stmtCarteira);

		mysqli_stmt_bind_param($stmtBanco, 'i', $bancoId);
		$ok = $ok && mysqli_stmt_execute($stmtBanco);
		mysqli_stmt_close($stmtBanco);

		return $ok;
	}
}
