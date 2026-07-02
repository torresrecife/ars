<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

class DashboardRepository
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
		$stmt = mysqli_prepare($this->connection, "SELECT banco_id, banco_cod, banco_name, banco_class FROM bancos WHERE banco_id = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		mysqli_stmt_bind_param($stmt, 'i', $bankId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row;
	}

	public function findWeekByMonthYear($month, $year)
	{
		$month = (int) $month;
		$year = (int) $year;
		$stmt = mysqli_prepare($this->connection, "SELECT * FROM semanas WHERE mes = ? AND ano = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		mysqli_stmt_bind_param($stmt, 'ii', $month, $year);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row;
	}

	public function listMetaRowsByBankMonthYearAndSpecies($bankId, $month, $year, $species, $excludeNames = array(), $includeNames = array())
	{
		$bankId = (int) $bankId;
		$month = (int) $month;
		$year = (int) $year;
		$species = (int) $species;

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
				a.nome,
				a.especie,
				a.anda_neo,
				a.ordem,
				a.chave
			FROM metas_andamentos AS m
			JOIN andamentos AS a ON a.anda_id = m.anda_id
			WHERE m.banco_id = ?
			AND m.meta_mes = ?
			AND m.meta_ano = ?
			AND a.especie = ?
		";

		$params = array($bankId, $month, $year, $species);
		$types = 'iiii';

		if (!empty($excludeNames)) {
			foreach ($excludeNames as $excludeName) {
				$sql .= " AND a.nome <> ?";
				$params[] = (string) $excludeName;
				$types .= 's';
			}
		}

		if (!empty($includeNames)) {
			$pieces = array();
			foreach ($includeNames as $includeName) {
				$pieces[] = "(a.nome = ? OR a.chave = ? OR a.anda_neo = ?)";
				$params[] = (string) $includeName;
				$params[] = (string) $includeName;
				$params[] = (string) $includeName;
				$types .= 'sss';
			}
			$sql .= " AND (" . implode(' OR ', $pieces) . ")";
		}

		$sql .= " ORDER BY a.especie ASC, a.ordem ASC, a.nome ASC";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return array();
		}

		$this->bindParams($stmt, $types, $params);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$rows = array();
		while ($result && ($row = mysqli_fetch_assoc($result))) {
			$rows[] = $row;
		}
		mysqli_stmt_close($stmt);

		return $rows;
	}

	public function findMetaRowByBankMonthYearAndAndaId($bankId, $month, $year, $andaId)
	{
		$bankId = (int) $bankId;
		$month = (int) $month;
		$year = (int) $year;
		$andaId = (int) $andaId;

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
				a.nome,
				a.especie,
				a.anda_neo,
				a.ordem,
				a.chave
			FROM metas_andamentos AS m
			JOIN andamentos AS a ON a.anda_id = m.anda_id
			WHERE m.banco_id = ?
			AND m.meta_mes = ?
			AND m.meta_ano = ?
			AND m.anda_id = ?
			LIMIT 1
		";

		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		mysqli_stmt_bind_param($stmt, 'iiii', $bankId, $month, $year, $andaId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row;
	}

	public function findCarteiraConditionByBankId($bankId)
	{
		$bankId = (int) $bankId;
		$stmt = mysqli_prepare($this->connection, "SELECT carteira_vinc FROM carteira WHERE banco_id = ? AND carteira_condicao = 'Carteira' LIMIT 1");
		if (!$stmt) {
			return '';
		}

		mysqli_stmt_bind_param($stmt, 'i', $bankId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row ? (string) $row['carteira_vinc'] : '';
	}

	public function listCarteiraCodesByBankId($bankId)
	{
		$bankId = (int) $bankId;
		$stmt = mysqli_prepare($this->connection, "SELECT d.dados_cod FROM dados AS d JOIN carteira AS c ON c.banco_id = d.banco_id WHERE d.banco_id = ?");
		if (!$stmt) {
			return array();
		}

		mysqli_stmt_bind_param($stmt, 'i', $bankId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$rows = array();
		while ($result && ($row = mysqli_fetch_assoc($result))) {
			$code = isset($row['dados_cod']) ? trim((string) $row['dados_cod']) : '';
			if ($code !== '') {
				$rows[$code] = $code;
			}
		}
		mysqli_stmt_close($stmt);

		return array_values($rows);
	}

	private function bindParams($stmt, $types, array $params)
	{
		$references = array();
		$references[] = $types;

		foreach ($params as $index => $value) {
			$references[] = &$params[$index];
		}

		call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt), $references));
	}
}
