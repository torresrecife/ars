<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

class MainPageRepository
{
	/** @var mysqli */
	private $connection;

	public function __construct(mysqli $connection)
	{
		$this->connection = $connection;
	}

	public function listAreas($userSectorId)
	{
		$sql = "SELECT area_id, area_nome FROM area WHERE area_status = 'Y'";
		$params = array();
		$types = '';

		if ((int) $userSectorId !== 0) {
			$sql .= " AND area_id = ?";
			$params[] = (int) $userSectorId;
			$types .= 'i';
		}

		$sql .= " ORDER BY area_nome";

		return $this->fetchAll($sql, $types, $params);
	}

	public function listBanksByArea($areaId, $userClientIds)
	{
		$sql = "SELECT banco_id, banco_name, banco_class
			FROM bancos
			WHERE banco_area = ?";
		$params = array((int) $areaId);
		$types = 'i';

		$clientIds = $this->parseIdList($userClientIds);
		if (!empty($clientIds)) {
			$sql .= " AND banco_id IN (" . implode(',', $clientIds) . ")";
		}

		$sql .= " AND banco_status IN ('Y','P') ORDER BY banco_area";

		return $this->fetchAll($sql, $types, $params);
	}

	public function listAreasForProduction($userLevel, $userSectorId)
	{
		$sql = "SELECT area_id, area_nome FROM area WHERE area_status = 'Y'";
		$params = array();
		$types = '';

		if ((string) $userLevel !== 'ADM') {
			$sql .= " AND area_id = ?";
			$params[] = (int) $userSectorId;
			$types .= 'i';
		}

		$sql .= " ORDER BY area_nome";

		return $this->fetchAll($sql, $types, $params);
	}

	public function listBanksForMetas($userSectorId, $userClientIds)
	{
		$sql = "SELECT banco_id, banco_name, banco_class
			FROM bancos
			WHERE banco_status = 'Y'";
		$params = array();
		$types = '';

		if ((int) $userSectorId !== 0) {
			$sql .= " AND banco_area IN (" . (int) $userSectorId . ")";
		}

		$clientIds = $this->parseIdList($userClientIds);
		if (!empty($clientIds)) {
			$sql .= " AND banco_id IN (" . implode(',', $clientIds) . ")";
		}

		$sql .= " ORDER BY banco_cod";

		return $this->fetchAll($sql, $types, $params);
	}

	public function listAdminBanks($userSectorId, $userClientIds)
	{
		$sql = "SELECT banco_id, banco_name
			FROM bancos
			WHERE banco_status = 'Y'";
		$params = array();
		$types = '';

		if ((int) $userSectorId !== 0) {
			$sql .= " AND banco_area IN (" . (int) $userSectorId . ")";
		}

		$clientIds = $this->parseIdList($userClientIds);
		if (!empty($clientIds)) {
			$sql .= " AND banco_id IN (" . implode(',', $clientIds) . ")";
		}

		$sql .= " ORDER BY banco_area, banco_name";

		return $this->fetchAll($sql, $types, $params);
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
