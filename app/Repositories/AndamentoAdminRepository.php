<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

class AndamentoAdminRepository
{
	/** @var mysqli */
	private $connection;

	public function __construct(mysqli $connection)
	{
		$this->connection = $connection;
	}

	public function findById($andamentoId)
	{
		$stmt = mysqli_prepare($this->connection, "SELECT * FROM andamentos WHERE anda_id = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		$andamentoId = (int) $andamentoId;
		mysqli_stmt_bind_param($stmt, 'i', $andamentoId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row;
	}

	public function listAll()
	{
		$result = mysqli_query($this->connection, "SELECT * FROM andamentos ORDER BY especie ASC, nome ASC");
		if ($result === false) {
			return array();
		}

		$rows = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function existsByKeyOrName($nome, $chave, $excludeId = 0)
	{
		$sql = "SELECT anda_id FROM andamentos WHERE (nome = ? OR chave = ?)";
		$types = 'ss';
		$params = array((string) $nome, (string) $chave);

		if ((int) $excludeId > 0) {
			$sql .= " AND anda_id <> ?";
			$types .= 'i';
			$params[] = (int) $excludeId;
		}

		$sql .= " LIMIT 1";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		$this->bindParams($stmt, $types, $params);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return is_array($row);
	}

	public function insert(array $data)
	{
		$stmt = mysqli_prepare(
			$this->connection,
			"INSERT INTO andamentos (nome, chave, anda_neo, especie, painel, titulo) VALUES (?, ?, ?, ?, ?, ?)"
		);
		if (!$stmt) {
			return false;
		}

		$nome = (string) $data['nome'];
		$chave = (string) $data['chave'];
		$andaNeo = (string) $data['anda_neo'];
		$especie = (string) $data['especie'];
		$painel = (string) $data['painel'];
		$titulo = (string) $data['titulo'];
		mysqli_stmt_bind_param($stmt, 'sssiss', $nome, $chave, $andaNeo, $especie, $painel, $titulo);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	public function update($andamentoId, array $data)
	{
		$stmt = mysqli_prepare(
			$this->connection,
			"UPDATE andamentos SET nome = ?, chave = ?, anda_neo = ?, especie = ?, painel = ?, titulo = ? WHERE anda_id = ? LIMIT 1"
		);
		if (!$stmt) {
			return false;
		}

		$andamentoId = (int) $andamentoId;
		$nome = (string) $data['nome'];
		$chave = (string) $data['chave'];
		$andaNeo = (string) $data['anda_neo'];
		$especie = (string) $data['especie'];
		$painel = (string) $data['painel'];
		$titulo = (string) $data['titulo'];
		mysqli_stmt_bind_param($stmt, 'sssissi', $nome, $chave, $andaNeo, $especie, $painel, $titulo, $andamentoId);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	public function delete($andamentoId)
	{
		$stmt = mysqli_prepare($this->connection, "DELETE FROM andamentos WHERE anda_id = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		$andamentoId = (int) $andamentoId;
		mysqli_stmt_bind_param($stmt, 'i', $andamentoId);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	private function bindParams($stmt, $types, array $params)
	{
		$references = array($types);
		foreach ($params as $index => $value) {
			$references[] = &$params[$index];
		}

		call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt), $references));
	}
}
