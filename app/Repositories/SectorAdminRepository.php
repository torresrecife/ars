<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

class SectorAdminRepository
{
	/** @var mysqli */
	private $connection;

	public function __construct(mysqli $connection)
	{
		$this->connection = $connection;
	}

	public function listAll()
	{
		$result = mysqli_query($this->connection, "SELECT * FROM area ORDER BY area_id");
		if ($result === false) {
			return array();
		}

		$rows = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function findById($areaId)
	{
		$stmt = mysqli_prepare($this->connection, "SELECT * FROM area WHERE area_id = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		$areaId = (int) $areaId;
		mysqli_stmt_bind_param($stmt, 'i', $areaId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row;
	}

	public function existsByName($name, $excludeId = 0)
	{
		$sql = "SELECT area_id FROM area WHERE area_nome = ?";
		$types = 's';
		$params = array((string) $name);

		if ((int) $excludeId > 0) {
			$sql .= " AND area_id <> ?";
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

	public function insert($name)
	{
		$stmt = mysqli_prepare($this->connection, "INSERT INTO area (area_nome, area_date) VALUES (?, ?)");
		if (!$stmt) {
			return false;
		}

		$name = (string) $name;
		$date = date('Y-m-d H:i:s');
		mysqli_stmt_bind_param($stmt, 'ss', $name, $date);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	public function update($areaId, $name)
	{
		$stmt = mysqli_prepare($this->connection, "UPDATE area SET area_nome = ? WHERE area_id = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		$areaId = (int) $areaId;
		$name = (string) $name;
		mysqli_stmt_bind_param($stmt, 'si', $name, $areaId);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	public function delete($areaId)
	{
		$stmt = mysqli_prepare($this->connection, "DELETE FROM area WHERE area_id = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		$areaId = (int) $areaId;
		mysqli_stmt_bind_param($stmt, 'i', $areaId);
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
