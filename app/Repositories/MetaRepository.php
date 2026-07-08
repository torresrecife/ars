<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

class MetaRepository
{
	/** @var mysqli */
	private $connection;

	public function __construct(mysqli $connection)
	{
		$this->connection = $connection;
	}

	public function findBankById($bankId)
	{
		$bankId = (int) $bankId;
		$sql = "SELECT banco_id, banco_cod, banco_name, banco_class FROM bancos WHERE banco_id = ? LIMIT 1";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		mysqli_stmt_bind_param($stmt, 'i', $bankId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$bank = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $bank;
	}

	public function listByBankMonthYear($bankId, $month, $year)
	{
		$bankId = (int) $bankId;
		$month = (int) $month;
		$year = (int) $year;

		$sql = "
			SELECT
				m.meta_id,
				m.banco_id,
				m.meta_mes,
				m.meta_ano,
				m.anda_id,
				m.meta_valor,
				m.def_sem,
				m.sem_1,
				m.sem_2,
				m.sem_3,
				m.sem_4,
				m.sem_5,
				m.regiao_id,
				a.nome,
				a.especie,
				b.banco_name,
				r.regiao_nome
			FROM metas_andamentos AS m
			JOIN andamentos AS a ON a.anda_id = m.anda_id
			JOIN bancos AS b ON b.banco_id = m.banco_id
			LEFT JOIN regioes AS r ON r.regiao_id = m.regiao_id
			WHERE m.banco_id = ?
			AND m.meta_mes = ?
			AND m.meta_ano = ?
			ORDER BY a.especie ASC, a.nome ASC, r.regiao_nome ASC, m.meta_id ASC
		";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return array();
		}

		mysqli_stmt_bind_param($stmt, 'iii', $bankId, $month, $year);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$rows = array();
		while ($result && ($row = mysqli_fetch_assoc($result))) {
			$rows[] = $row;
		}
		mysqli_stmt_close($stmt);

		return $rows;
	}

	public function findById($metaId)
	{
		$metaId = (int) $metaId;
		$sql = "SELECT * FROM metas_andamentos WHERE meta_id = ? LIMIT 1";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		mysqli_stmt_bind_param($stmt, 'i', $metaId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row;
	}

	public function listAndamentos()
	{
		$result = mysqli_query($this->connection, "SELECT * FROM andamentos AS a ORDER BY a.especie ASC, a.nome ASC");
		$rows = array();
		while ($result && ($row = mysqli_fetch_assoc($result))) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function existsDuplicate(array $data, $excludeMetaId = 0)
	{
		$sql = "
			SELECT meta_id
			FROM metas_andamentos
			WHERE banco_id = ?
			AND meta_mes = ?
			AND meta_ano = ?
			AND anda_id = ?
		";

		$params = array(
			(int) $data['banco_id'],
			(int) $data['meta_mes'],
			(int) $data['meta_ano'],
			(int) $data['anda_id'],
		);
		$types = 'iiii';
		$regionId = isset($data['regiao_id']) && (int) $data['regiao_id'] > 0 ? (int) $data['regiao_id'] : 0;

		if ($regionId > 0) {
			$sql .= " AND regiao_id = ?";
			$params[] = $regionId;
			$types .= 'i';
		} else {
			$sql .= " AND regiao_id IS NULL";
		}

		if ((int) $excludeMetaId > 0) {
			$sql .= " AND meta_id <> ?";
			$params[] = (int) $excludeMetaId;
			$types .= 'i';
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
		$sql = "
			INSERT INTO metas_andamentos (
				banco_id, meta_mes, meta_ano, anda_id, def_sem,
				sem_1, sem_2, sem_3, sem_4, sem_5, meta_valor, regiao_id
			) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
		";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		$bancoId = (int) $data['banco_id'];
		$metaMes = (int) $data['meta_mes'];
		$metaAno = (int) $data['meta_ano'];
		$andaId = (int) $data['anda_id'];
		$defSem = (string) $data['def_sem'];
		$sem1 = $data['sem_1'];
		$sem2 = $data['sem_2'];
		$sem3 = $data['sem_3'];
		$sem4 = $data['sem_4'];
		$sem5 = $data['sem_5'];
		$metaValor = $data['meta_valor'];
		$regiaoId = isset($data['regiao_id']) && (int) $data['regiao_id'] > 0 ? (int) $data['regiao_id'] : null;

		mysqli_stmt_bind_param($stmt, 'iiiisddddddi', $bancoId, $metaMes, $metaAno, $andaId, $defSem, $sem1, $sem2, $sem3, $sem4, $sem5, $metaValor, $regiaoId);
		$inserted = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $inserted;
	}

	public function update($metaId, array $data)
	{
		$sql = "
			UPDATE metas_andamentos SET
				banco_id = ?, meta_mes = ?, meta_ano = ?, anda_id = ?, def_sem = ?,
				sem_1 = ?, sem_2 = ?, sem_3 = ?, sem_4 = ?, sem_5 = ?, meta_valor = ?, regiao_id = ?
			WHERE meta_id = ? LIMIT 1
		";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		$bancoId = (int) $data['banco_id'];
		$metaMes = (int) $data['meta_mes'];
		$metaAno = (int) $data['meta_ano'];
		$andaId = (int) $data['anda_id'];
		$defSem = (string) $data['def_sem'];
		$sem1 = $data['sem_1'];
		$sem2 = $data['sem_2'];
		$sem3 = $data['sem_3'];
		$sem4 = $data['sem_4'];
		$sem5 = $data['sem_5'];
		$metaValor = $data['meta_valor'];
		$regiaoId = isset($data['regiao_id']) && (int) $data['regiao_id'] > 0 ? (int) $data['regiao_id'] : null;
		$metaId = (int) $metaId;

		mysqli_stmt_bind_param($stmt, 'iiiisddddddii', $bancoId, $metaMes, $metaAno, $andaId, $defSem, $sem1, $sem2, $sem3, $sem4, $sem5, $metaValor, $regiaoId, $metaId);
		$updated = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $updated;
	}

	public function delete($metaId)
	{
		$metaId = (int) $metaId;
		$stmt = mysqli_prepare($this->connection, "DELETE FROM metas_andamentos WHERE meta_id = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		mysqli_stmt_bind_param($stmt, 'i', $metaId);
		$deleted = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $deleted;
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
