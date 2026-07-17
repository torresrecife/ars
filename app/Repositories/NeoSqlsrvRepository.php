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
		'AC' => 'Acre',
		'AL' => 'Alagoas',
		'AP' => 'AmapÃ¡',
		'AM' => 'Amazonas',
		'BA' => 'Bahia',
		'CE' => 'CearÃ¡',
		'DF' => 'Distrito Federal',
		'ES' => 'EspÃ­rito Santo',
		'GO' => 'GoiÃ¡s',
		'MA' => 'MaranhÃ£o',
		'MT' => 'Mato Grosso',
		'MS' => 'Mato Grosso do Sul',
		'MG' => 'Minas Gerais',
		'PA' => 'ParÃ¡',
		'PB' => 'ParaÃ­ba',
		'PR' => 'ParanÃ¡',
		'PE' => 'Pernambuco',
		'PI' => 'PiauÃ­',
		'RJ' => 'Rio de Janeiro',
		'RN' => 'Rio Grande do Norte',
		'RS' => 'Rio Grande do Sul',
		'RO' => 'RondÃ´nia',
		'RR' => 'Roraima',
		'SC' => 'Santa Catarina',
		'SP' => 'SÃ£o Paulo',
		'SE' => 'Sergipe',
		'TO' => 'Tocantins',
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
		return ' AND MONTH(' . $field . ') = ' . (int) $month
			. ' AND YEAR(' . $field . ') = ' . (int) $year;
	}

	protected function buildWeekCondition($field, $month, $year, array $week = array())
	{
		$query = $this->buildMonthYearCondition($field, $month, $year);

		if (isset($week['start'], $week['end'])) {
			$query .= ' AND DAY(' . $field . ') >= ' . (int) $week['start'];
			$query .= ' AND DAY(' . $field . ') <= ' . (int) $week['end'];
		}

		return $query;
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
				$values[$this->ufNameMap[$upperCode]] = $this->ufNameMap[$upperCode];
			}
		}

		return array_values($values);
	}
}
