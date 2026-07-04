<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

class RegionRepository
{
	/** @var mysqli */
	private $connection;

	public function __construct(mysqli $connection)
	{
		$this->connection = $connection;
	}

	public function listActiveRegions()
	{
		$result = mysqli_query(
			$this->connection,
			"SELECT regiao_id, regiao_nome, regiao_slug, regiao_status FROM regioes WHERE regiao_status = 'Y' ORDER BY regiao_nome"
		);
		if (!$result) {
			return array();
		}

		$rows = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function listRegionsByIds(array $ids)
	{
		$cleanIds = $this->cleanIds($ids);
		if (empty($cleanIds)) {
			return array();
		}

		$sql = "SELECT regiao_id, regiao_nome, regiao_slug, regiao_status FROM regioes WHERE regiao_id IN (" . implode(',', $cleanIds) . ") ORDER BY regiao_nome";
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

	public function listRegionIdsByUserId($userId)
	{
		$stmt = mysqli_prepare($this->connection, "SELECT regiao_id FROM usuarios_regioes WHERE usuario_id = ? ORDER BY regiao_id");
		if (!$stmt) {
			return array();
		}

		$userId = (int) $userId;
		mysqli_stmt_bind_param($stmt, 'i', $userId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$ids = array();
		while ($result && ($row = mysqli_fetch_assoc($result))) {
			$regionId = isset($row['regiao_id']) ? (int) $row['regiao_id'] : 0;
			if ($regionId > 0) {
				$ids[$regionId] = $regionId;
			}
		}
		mysqli_stmt_close($stmt);

		return array_values($ids);
	}

	public function listUfCodesByUserId($userId)
	{
		$stmt = mysqli_prepare(
			$this->connection,
			"SELECT DISTINCT ru.uf
			FROM usuarios_regioes AS ur
			JOIN regioes_ufs AS ru ON ru.regiao_id = ur.regiao_id
			WHERE ur.usuario_id = ?
			ORDER BY ru.uf"
		);
		if (!$stmt) {
			return array();
		}

		$userId = (int) $userId;
		mysqli_stmt_bind_param($stmt, 'i', $userId);
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

	public function listUfCodesByRegionIds(array $regionIds)
	{
		$cleanIds = $this->cleanIds($regionIds);
		if (empty($cleanIds)) {
			return array();
		}

		$sql = "SELECT DISTINCT uf FROM regioes_ufs WHERE regiao_id IN (" . implode(',', $cleanIds) . ") ORDER BY uf";
		$result = mysqli_query($this->connection, $sql);
		if (!$result) {
			return array();
		}

		$ufs = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$uf = isset($row['uf']) ? strtoupper(trim((string) $row['uf'])) : '';
			if ($uf !== '') {
				$ufs[$uf] = $uf;
			}
		}

		return array_values($ufs);
	}

	public function replaceUserRegions($userId, array $regionIds)
	{
		$userId = (int) $userId;
		if ($userId <= 0) {
			return false;
		}

		$cleanIds = $this->cleanIds($regionIds);
		$deleteStmt = mysqli_prepare($this->connection, "DELETE FROM usuarios_regioes WHERE usuario_id = ?");
		if (!$deleteStmt) {
			return false;
		}

		mysqli_begin_transaction($this->connection);

		mysqli_stmt_bind_param($deleteStmt, 'i', $userId);
		$ok = mysqli_stmt_execute($deleteStmt);
		mysqli_stmt_close($deleteStmt);

		if ($ok && !empty($cleanIds)) {
			$insertStmt = mysqli_prepare($this->connection, "INSERT INTO usuarios_regioes (usuario_id, regiao_id) VALUES (?, ?)");
			if (!$insertStmt) {
				mysqli_rollback($this->connection);
				return false;
			}

			foreach ($cleanIds as $regionId) {
				mysqli_stmt_bind_param($insertStmt, 'ii', $userId, $regionId);
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

	private function cleanIds(array $ids)
	{
		$clean = array();
		foreach ($ids as $id) {
			$id = (int) $id;
			if ($id > 0) {
				$clean[$id] = $id;
			}
		}

		return array_values($clean);
	}
}
