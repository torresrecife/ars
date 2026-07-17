<?php

declare(strict_types=1);

namespace App\Repositories;

class NeoSqlsrvExecutor
{
	/** @var mixed */
	private $connection;

	public function __construct($connection)
	{
		$this->connection = $connection;
	}

	public function fetchAll($query)
	{
		$result = sqlsrv_query($this->connection, $query);
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
		$result = sqlsrv_query($this->connection, $query);
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

	public function sumAndCollectCodes($query, $valueField, $codeField)
	{
		$result = sqlsrv_query($this->connection, $query);
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
}
