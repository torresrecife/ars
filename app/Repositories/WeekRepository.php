<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

class WeekRepository
{
	/** @var mysqli */
	private $connection;

	public function __construct(mysqli $connection)
	{
		$this->connection = $connection;
	}

	public function all()
	{
		$result = mysqli_query($this->connection, "SELECT *, DATE_FORMAT(data_cad, '%d/%m/%Y %H:%i:%s') AS datacad, DATE_FORMAT(data_cad, '%d/%m/%Y %H:%i:%s') AS dataalt FROM semanas ORDER BY semanas_id");
		$rows = array();
		while ($result && ($row = mysqli_fetch_assoc($result))) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function findById($weekId)
	{
		$weekId = (int) $weekId;
		$stmt = mysqli_prepare($this->connection, "SELECT * FROM semanas WHERE semanas_id = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		mysqli_stmt_bind_param($stmt, 'i', $weekId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row;
	}

	public function existsByMonthYear($month, $year, $ignoreId = null)
	{
		$month = (int) $month;
		$year = (int) $year;
		$sql = "SELECT semanas_id FROM semanas WHERE mes = ? AND ano = ?";
		if ($ignoreId !== null) {
			$sql .= " AND semanas_id <> " . (int) $ignoreId;
		}
		$sql .= " LIMIT 1";

		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		mysqli_stmt_bind_param($stmt, 'ii', $month, $year);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$exists = $result && mysqli_fetch_assoc($result);
		mysqli_stmt_close($stmt);

		return (bool) $exists;
	}

	public function insert(array $data)
	{
		$sql = "
			INSERT INTO semanas (
				mes, ano, ini_1, fim_1, ini_2, fim_2, ini_3, fim_3, ini_4, fim_4, ini_5, fim_5, data_cad, data_arlt
			) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
		";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		mysqli_stmt_bind_param(
			$stmt,
			'iiiiiiiiiiiiss',
			$data['mes'],
			$data['ano'],
			$data['ini_1'],
			$data['fim_1'],
			$data['ini_2'],
			$data['fim_2'],
			$data['ini_3'],
			$data['fim_3'],
			$data['ini_4'],
			$data['fim_4'],
			$data['ini_5'],
			$data['fim_5'],
			$data['data_cad'],
			$data['data_arlt']
		);
		$inserted = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $inserted;
	}

	public function update($weekId, array $data)
	{
		$weekId = (int) $weekId;
		$sql = "
			UPDATE semanas SET
				mes = ?, ano = ?, ini_1 = ?, fim_1 = ?, ini_2 = ?, fim_2 = ?, ini_3 = ?, fim_3 = ?,
				ini_4 = ?, fim_4 = ?, ini_5 = ?, fim_5 = ?, data_arlt = ?
			WHERE semanas_id = ? LIMIT 1
		";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		mysqli_stmt_bind_param(
			$stmt,
			'iiiiiiiiiiiisi',
			$data['mes'],
			$data['ano'],
			$data['ini_1'],
			$data['fim_1'],
			$data['ini_2'],
			$data['fim_2'],
			$data['ini_3'],
			$data['fim_3'],
			$data['ini_4'],
			$data['fim_4'],
			$data['ini_5'],
			$data['fim_5'],
			$data['data_arlt'],
			$weekId
		);
		$updated = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $updated;
	}

	public function delete($weekId)
	{
		$weekId = (int) $weekId;
		$stmt = mysqli_prepare($this->connection, "DELETE FROM semanas WHERE semanas_id = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		mysqli_stmt_bind_param($stmt, 'i', $weekId);
		$deleted = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $deleted;
	}
}
