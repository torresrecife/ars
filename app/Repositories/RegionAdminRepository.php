<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

class RegionAdminRepository
{
	/** @var mysqli */
	private $connection;

	public function __construct(mysqli $connection)
	{
		$this->connection = $connection;
	}

	public function all()
	{
		$sql = "SELECT r.regiao_id, r.regiao_nome, r.regiao_slug, r.regiao_status,
			COALESCE(GROUP_CONCAT(ru.uf ORDER BY ru.uf SEPARATOR ', '), '') AS ufs
			FROM regioes AS r
			LEFT JOIN regioes_ufs AS ru ON ru.regiao_id = r.regiao_id
			GROUP BY r.regiao_id, r.regiao_nome, r.regiao_slug, r.regiao_status
			ORDER BY r.regiao_nome";
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

	public function findById($id)
	{
		$stmt = mysqli_prepare($this->connection, "SELECT regiao_id, regiao_nome, regiao_slug, regiao_status FROM regioes WHERE regiao_id = ? LIMIT 1");
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

	public function findBySlug($slug, $excludeId = 0)
	{
		$sql = "SELECT regiao_id FROM regioes WHERE regiao_slug = ?";
		$types = 's';
		$params = array((string) $slug);

		if ((int) $excludeId > 0) {
			$sql .= " AND regiao_id <> ?";
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

		return $row;
	}

	public function listUfsByRegionId($regionId)
	{
		$stmt = mysqli_prepare($this->connection, "SELECT uf FROM regioes_ufs WHERE regiao_id = ? ORDER BY uf");
		if (!$stmt) {
			return array();
		}

		$regionId = (int) $regionId;
		mysqli_stmt_bind_param($stmt, 'i', $regionId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$ufs = array();
		while ($result && ($row = mysqli_fetch_assoc($result))) {
			$uf = isset($row['uf']) ? strtoupper(trim((string) $row['uf'])) : '';
			if ($uf !== '') {
				$ufs[$uf] = $uf;
			}
		}
		mysqli_stmt_close($stmt);

		return array_values($ufs);
	}

	public function insert(array $data)
	{
		$stmt = mysqli_prepare($this->connection, "INSERT INTO regioes (regiao_nome, regiao_slug, regiao_status, data_cad) VALUES (?, ?, ?, NOW())");
		if (!$stmt) {
			return false;
		}

		$name = (string) $data['regiao_nome'];
		$slug = (string) $data['regiao_slug'];
		$status = (string) $data['regiao_status'];
		mysqli_stmt_bind_param($stmt, 'sss', $name, $slug, $status);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	public function update(array $data)
	{
		$stmt = mysqli_prepare($this->connection, "UPDATE regioes SET regiao_nome = ?, regiao_slug = ?, regiao_status = ?, data_alt = NOW() WHERE regiao_id = ?");
		if (!$stmt) {
			return false;
		}

		$id = (int) $data['regiao_id'];
		$name = (string) $data['regiao_nome'];
		$slug = (string) $data['regiao_slug'];
		$status = (string) $data['regiao_status'];
		mysqli_stmt_bind_param($stmt, 'sssi', $name, $slug, $status, $id);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	public function delete($id)
	{
		$stmt = mysqli_prepare($this->connection, "DELETE FROM regioes WHERE regiao_id = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		$id = (int) $id;
		mysqli_stmt_bind_param($stmt, 'i', $id);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	public function replaceUfs($regionId, array $ufs)
	{
		$regionId = (int) $regionId;
		if ($regionId <= 0) {
			return false;
		}

		$ufs = $this->normalizeUfs($ufs);
		$deleteStmt = mysqli_prepare($this->connection, "DELETE FROM regioes_ufs WHERE regiao_id = ?");
		if (!$deleteStmt) {
			return false;
		}

		mysqli_begin_transaction($this->connection);
		mysqli_stmt_bind_param($deleteStmt, 'i', $regionId);
		$ok = mysqli_stmt_execute($deleteStmt);
		mysqli_stmt_close($deleteStmt);

		if ($ok && !empty($ufs)) {
			$insertStmt = mysqli_prepare($this->connection, "INSERT INTO regioes_ufs (regiao_id, uf) VALUES (?, ?)");
			if (!$insertStmt) {
				mysqli_rollback($this->connection);
				return false;
			}

			foreach ($ufs as $uf) {
				mysqli_stmt_bind_param($insertStmt, 'is', $regionId, $uf);
				if (!mysqli_stmt_execute($insertStmt)) {
					$ok = false;
					break;
				}
			}
			mysqli_stmt_close($insertStmt);
		}

		if ($ok) {
			mysqli_commit($this->connection);
			return true;
		}

		mysqli_rollback($this->connection);
		return false;
	}

	public function lastInsertId()
	{
		return (int) mysqli_insert_id($this->connection);
	}

	private function normalizeUfs(array $ufs)
	{
		$clean = array();
		foreach ($ufs as $uf) {
			$uf = strtoupper(trim((string) $uf));
			if (preg_match('/^[A-Z]{2}$/', $uf)) {
				$clean[$uf] = $uf;
			}
		}

		return array_values($clean);
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
