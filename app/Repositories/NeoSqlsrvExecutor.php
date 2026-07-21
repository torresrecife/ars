<?php

declare(strict_types=1);

namespace App\Repositories;

class NeoSqlsrvExecutor
{
	/** @var mixed */
	private $connection;

	/** @var array */
	private static $stats = array(
		'queries' => 0,
		'errors' => 0,
		'time_ms' => 0.0,
	);

	public function __construct($connection)
	{
		$this->connection = $connection;
	}

	public static function resetStats()
	{
		self::$stats = array(
			'queries' => 0,
			'errors' => 0,
			'time_ms' => 0.0,
		);
	}

	public static function stats()
	{
		return self::$stats;
	}

	public function fetchAll($query)
	{
		$result = $this->runQuery($query);
		if ($result === false) {
			return array();
		}

		$rows = array();
		while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function fetchCodes($query, $field)
	{
		$result = $this->runQuery($query);
		if ($result === false) {
			return array();
		}

		$codes = array();
		while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
			if (isset($row[$field])) {
				$codes[] = $row[$field];
			}
		}

		return $codes;
	}

	public function countRows($query, $field)
	{
		$result = $this->runQuery($query);
		if ($result === false) {
			return 0;
		}

		$total = 0;
		while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
			$total += isset($row[$field]) ? (int) $row[$field] : 0;
		}

		return $total;
	}

	public function sumAndCollectCodes($query, $valueField, $codeField)
	{
		$result = $this->runQuery($query);
		if ($result === false) {
			return array('total' => 0.0, 'codes' => array());
		}

		$total = 0.0;
		$codes = array();
		while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
			$total += isset($row[$valueField]) ? (float) $row[$valueField] : 0.0;
			if (isset($row[$codeField])) {
				$codes[(string) $row[$codeField]] = (string) $row[$codeField];
			}
		}

		return array(
			'total' => $total,
			'codes' => array_values($codes),
		);
	}

	private function runQuery($query)
	{
		$startedAt = microtime(true);
		$result = sqlsrv_query($this->connection, $query);
		$elapsedMs = (microtime(true) - $startedAt) * 1000;

		self::$stats['queries']++;
		self::$stats['time_ms'] += $elapsedMs;
		if ($result === false) {
			self::$stats['errors']++;
		}

		return $result;
	}
}
