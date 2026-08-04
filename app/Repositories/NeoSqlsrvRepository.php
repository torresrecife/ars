<?php

declare(strict_types=1);

namespace App\Repositories;

abstract class NeoSqlsrvRepository
{
	/** @var mixed */
	protected $connection;

	/** @var NeoSqlsrvExecutor */
	private $executor;

	/** @var array */
	private $ufNameMap = array(
		'AC' => array('Acre'),
		'AL' => array('Alagoas'),
		'AP' => array('Amapa', 'Amapá'),
		'AM' => array('Amazonas'),
		'BA' => array('Bahia'),
		'CE' => array('Ceara', 'Ceará'),
		'DF' => array('Distrito Federal'),
		'ES' => array('Espirito Santo', 'Espírito Santo'),
		'GO' => array('Goias', 'Goiás'),
		'MA' => array('Maranhao', 'Maranhão'),
		'MT' => array('Mato Grosso'),
		'MS' => array('Mato Grosso do Sul'),
		'MG' => array('Minas Gerais'),
		'PA' => array('Para', 'Pará'),
		'PB' => array('Paraiba', 'Paraíba'),
		'PR' => array('Parana', 'Paraná'),
		'PE' => array('Pernambuco'),
		'PI' => array('Piaui', 'Piauí'),
		'RJ' => array('Rio de Janeiro'),
		'RN' => array('Rio Grande do Norte'),
		'RS' => array('Rio Grande do Sul'),
		'RO' => array('Rondonia', 'Rondônia'),
		'RR' => array('Roraima'),
		'SC' => array('Santa Catarina'),
		'SP' => array('Sao Paulo', 'São Paulo'),
		'SE' => array('Sergipe'),
		'TO' => array('Tocantins'),
	);

	public function __construct($connection)
	{
		$this->connection = $connection;
		$this->executor = new NeoSqlsrvExecutor($connection);
	}

	protected function buildQuotedList(array $values)
	{
		$clean = array();
		foreach ($values as $value) {
			$value = trim((string) $value);
			if ($value !== '') {
				$clean[$value] = "'" . str_replace("'", "''", $value) . "'";
			}
		}

		return implode(',', $clean);
	}

	protected function buildIntList(array $codes)
	{
		$ids = array();
		foreach ($codes as $code) {
			$code = (int) $code;
			if ($code > 0) {
				$ids[$code] = $code;
			}
		}

		return implode(',', $ids);
	}

	protected function buildCarteiraCondition(array $carteiraCodes, $carteiraMode, $field = 'p.Carteira')
	{
		if (empty($carteiraCodes)) {
			return ' AND 1 = 0';
		}

		if ($carteiraMode === 'LIKE') {
			return ' AND ' . $field . " LIKE '%" . str_replace("'", '', implode(',', $carteiraCodes)) . "%'";
		}

		return ' AND ' . $field . ' IN (' . $this->buildQuotedList($carteiraCodes) . ')';
	}

	protected function buildUfCondition(array $ufCodes, $field = 'p.UFComarca')
	{
		$quoted = $this->buildQuotedList($this->expandUfCodes($ufCodes));
		if ($quoted === '') {
			return '';
		}

		return ' AND ' . $field . ' IN (' . $quoted . ')';
	}

	protected function buildMonthYearCondition($field, $month, $year)
	{
		$range = $this->monthDateRange((int) $month, (int) $year);

		return ' AND ' . $field . " >= '" . $range['start'] . "'"
			. ' AND ' . $field . " < '" . $range['end'] . "'";
	}

	protected function buildWeekCondition($field, $month, $year, array $week = array())
	{
		if (isset($week['start'], $week['end'])) {
			$range = $this->weekDateRange((int) $month, (int) $year, (int) $week['start'], (int) $week['end']);

			return ' AND ' . $field . " >= '" . $range['start'] . "'"
				. ' AND ' . $field . " < '" . $range['end'] . "'";
		}

		return $this->buildMonthYearCondition($field, $month, $year);
	}

	protected function fetchAll($query)
	{
		return $this->executor->fetchAll($query);
	}

	protected function sumQuery($query, $valueField, $codeField)
	{
		return $this->executor->sumAndCollectCodes($query, $valueField, $codeField);
	}

	protected function codeQuery($query, $field)
	{
		return $this->executor->fetchCodes($query, $field);
	}

	protected function countQuery($query, $field)
	{
		return $this->executor->countRows($query, $field);
	}

	private function expandUfCodes(array $ufCodes)
	{
		$values = array();
		foreach ($ufCodes as $code) {
			$code = trim((string) $code);
			if ($code === '') {
				continue;
			}

			$upperCode = strtoupper($code);
			$values[$upperCode] = $upperCode;
			if (isset($this->ufNameMap[$upperCode])) {
				foreach ($this->ufNameMap[$upperCode] as $name) {
					$values[$name] = $name;
				}
			}
		}

		return array_values($values);
	}

	private function monthDateRange($month, $year)
	{
		$month = max(1, min(12, (int) $month));
		$year = max(1900, (int) $year);
		$start = new \DateTimeImmutable(sprintf('%04d-%02d-01 00:00:00', $year, $month));

		return array(
			'start' => $start->format('Y-m-d H:i:s'),
			'end' => $start->modify('+1 month')->format('Y-m-d H:i:s'),
		);
	}

	private function weekDateRange($month, $year, $startDay, $endDay)
	{
		$month = max(1, min(12, (int) $month));
		$year = max(1900, (int) $year);
		$lastDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$startDay = max(1, min($lastDay, (int) $startDay));
		$endDay = max($startDay, min($lastDay, (int) $endDay));

		$start = new \DateTimeImmutable(sprintf('%04d-%02d-%02d 00:00:00', $year, $month, $startDay));
		$end = new \DateTimeImmutable(sprintf('%04d-%02d-%02d 00:00:00', $year, $month, $endDay));

		return array(
			'start' => $start->format('Y-m-d H:i:s'),
			'end' => $end->modify('+1 day')->format('Y-m-d H:i:s'),
		);
	}
}
