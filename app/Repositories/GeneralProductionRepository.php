<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

class GeneralProductionRepository
{
	/** @var mysqli */
	private $connection;

	public function __construct(mysqli $connection)
	{
		$this->connection = $connection;
	}

	public function findWeekByMonthYear($month, $year)
	{
		$stmt = mysqli_prepare($this->connection, "SELECT * FROM semanas WHERE mes = ? AND ano = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		$month = (int) $month;
		$year = (int) $year;
		mysqli_stmt_bind_param($stmt, 'ii', $month, $year);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row;
	}

	public function findAreaNameById($areaId)
	{
		$stmt = mysqli_prepare($this->connection, "SELECT area_nome FROM area WHERE area_id = ? LIMIT 1");
		if (!$stmt) {
			return '';
		}

		$areaId = (int) $areaId;
		mysqli_stmt_bind_param($stmt, 'i', $areaId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row ? (string) $row['area_nome'] : '';
	}

	public function listBanks($userSectorId, $startSector, $userClientIds, $activeOnly)
	{
		$sql = "SELECT banco_id, banco_name, banco_class, banco_status FROM bancos WHERE 1=1";
		$params = array();
		$types = '';

		if ($activeOnly) {
			$sql .= " AND banco_status = 'Y'";
		}

		if ((int) $userSectorId !== 0) {
			$sql .= " AND banco_area = ?";
			$params[] = (int) $userSectorId;
			$types .= 'i';
		} elseif ((string) $startSector !== '') {
			$sql .= " AND banco_area = ?";
			$params[] = (int) $startSector;
			$types .= 'i';
		}

		$clientIds = $this->parseIdList($userClientIds);
		if (!empty($clientIds)) {
			$sql .= " AND banco_id IN (" . implode(',', $clientIds) . ")";
		}

		$sql .= " ORDER BY banco_name ASC";

		return $this->fetchAll($sql, $types, $params);
	}

	public function listFinancialMetasByBankMonthYear($bankId, $month, $year, $regionId = 0)
	{
		$sql = "
			SELECT
				m.meta_id,
				m.meta_valor,
				m.def_sem,
				m.sem_1,
				m.sem_2,
				m.sem_3,
				m.sem_4,
				m.sem_5,
				m.regiao_id,
				a.anda_neo
			FROM metas_andamentos AS m
			JOIN andamentos AS a ON a.anda_id = m.anda_id
			WHERE m.banco_id = ?
			AND m.meta_mes = ?
			AND m.meta_ano = ?
			AND a.especie = 2
		";

		$params = array((int) $bankId, (int) $month, (int) $year);
		$types = 'iii';

		if ((int) $regionId > 0) {
			$sql .= " AND m.regiao_id = ?";
			$params[] = (int) $regionId;
			$types .= 'i';
		} else {
			$sql .= " AND m.regiao_id IS NULL";
		}

		$sql .= " ORDER BY m.meta_id ASC";

		return $this->fetchAll($sql, $types, $params);
	}

	public function findCarteiraModeByBankId($bankId)
	{
		$stmt = mysqli_prepare($this->connection, "SELECT carteira_vinc FROM carteira WHERE banco_id = ? AND carteira_condicao = 'Carteira' LIMIT 1");
		if (!$stmt) {
			return '';
		}

		$bankId = (int) $bankId;
		mysqli_stmt_bind_param($stmt, 'i', $bankId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row ? (string) $row['carteira_vinc'] : '';
	}

	public function listCarteiraCodesByBankId($bankId)
	{
		$sql = "
			SELECT d.dados_cod
			FROM dados AS d
			JOIN carteira AS c ON c.banco_id = d.banco_id
			WHERE d.banco_id = ?
		";

		$rows = $this->fetchAll($sql, 'i', array((int) $bankId));
		$codes = array();
		foreach ($rows as $row) {
			$code = isset($row['dados_cod']) ? trim((string) $row['dados_cod']) : '';
			if ($code !== '') {
				$codes[$code] = $code;
			}
		}

		return array_values($codes);
	}

	private function fetchAll($sql, $types = '', array $params = array())
	{
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return array();
		}

		if ($types !== '') {
			$this->bindParams($stmt, $types, $params);
		}

		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$rows = array();
		while ($result && ($row = mysqli_fetch_assoc($result))) {
			$rows[] = $row;
		}
		mysqli_stmt_close($stmt);

		return $rows;
	}

	private function bindParams($stmt, $types, array $params)
	{
		$references = array($types);
		foreach ($params as $index => $value) {
			$references[] = &$params[$index];
		}

		call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt), $references));
	}

	private function parseIdList($value)
	{
		$ids = array();
		foreach (explode(',', (string) $value) as $item) {
			$item = trim($item);
			if ($item !== '' && ctype_digit($item) && (int) $item > 0) {
				$ids[(int) $item] = (int) $item;
			}
		}

		return array_values($ids);
	}
}
