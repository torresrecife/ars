<?php

declare(strict_types=1);

namespace App\Repositories;

class NeoPanelRepository
{
	/** @var mixed */
	private $connection;

	public function __construct($connection)
	{
		$this->connection = $connection;
	}

	public function isAvailable()
	{
		return $this->connection !== null;
	}

	public function countProductionByWeek(array $typeNames, array $carteiraCodes, $carteiraMode, array $week, $month, $year, array $ufCodes = array())
	{
		if (!$this->isAvailable()) {
			return array('count' => 0, 'codes' => array());
		}

		$queryBase = $this->buildProductionBaseQuery($typeNames, $carteiraCodes, $carteiraMode, $week, $month, $year, $ufCodes);
		if ($queryBase === '') {
			return array('count' => 0, 'codes' => array());
		}

		$codes = array();
		$codeResult = sqlsrv_query($this->connection, "SELECT a.CodigoAndamento " . $queryBase);
		while ($codeResult && ($row = sqlsrv_fetch_array($codeResult, SQLSRV_FETCH_ASSOC))) {
			$codes[] = $row['CodigoAndamento'];
		}

		$count = 0;
		$countResult = sqlsrv_query($this->connection, "SELECT 1 as qtd " . $queryBase);
		while ($countResult && ($row = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC))) {
			$count += (int) $row['qtd'];
		}

		return array(
			'count' => $count,
			'codes' => $codes,
		);
	}

	public function sumFinancialByWeek(array $typeNames, array $carteiraCodes, $carteiraMode, array $week, $month, $year, array $ufCodes = array())
	{
		if (!$this->isAvailable()) {
			return array('total' => 0.0, 'codes' => array());
		}

		$query = "
			SELECT l.CodigoLancamento, l.Valor as qtd2
			FROM v_Processo AS p WITH (NOLOCK)
			JOIN v_Lancamento_Processo AS l WITH (NOLOCK) ON l.CodigoProcesso = p.CodigoProcesso
		";

		$where = $this->buildFinancialWhere($typeNames, $carteiraCodes, $carteiraMode, $week, $month, $year, $ufCodes);
		if ($where === '') {
			return array('total' => 0.0, 'codes' => array());
		}

		$query .= $where;
		$query .= " GROUP BY l.CodigoLancamento, l.Valor";

		$total = 0.0;
		$codes = array();
		$result = sqlsrv_query($this->connection, $query);
		while ($result && ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC))) {
			$total += (float) $row['qtd2'];
			$codes[] = $row['CodigoLancamento'];
		}

		return array(
			'total' => $total,
			'codes' => $codes,
		);
	}

	private function buildProductionBaseQuery(array $typeNames, array $carteiraCodes, $carteiraMode, array $week, $month, $year, array $ufCodes = array())
	{
		$typeList = $this->buildQuotedList($typeNames);
		if ($typeList === '') {
			return '';
		}

		$query = "
			FROM v_Andamento_Processo AS a WITH (NOLOCK)
			JOIN v_Processo AS p WITH (NOLOCK) ON p.CodigoProcesso = a.CodigoProcesso
			WHERE a.TipoAndamentoProcesso IN (" . $typeList . ")
			AND p.TipoProcesso NOT IN (N'CARTA PRECATÓRIA')
		";
		$query .= $this->buildCarteiraCondition($carteiraCodes, $carteiraMode);
		$query .= $this->buildUfCondition($ufCodes);
		$query .= " AND (DAY(a.DataHoraEvento) >= " . (int) $week['start'] . " AND DAY(a.DataHoraEvento) <= " . (int) $week['end'] . ")";
		$query .= " AND MONTH(a.DataHoraEvento) = " . (int) $month;
		$query .= " AND YEAR(a.DataHoraEvento) = " . (int) $year;
		$query .= " AND p.TipoDesdobramento IS NULL AND a.Invalido = 'False'";
		$query .= " GROUP BY a.CodigoAndamento";

		return $query;
	}

	private function buildFinancialWhere(array $typeNames, array $carteiraCodes, $carteiraMode, array $week, $month, $year, array $ufCodes = array())
	{
		$typeList = $this->buildQuotedList($typeNames);
		if ($typeList === '') {
			return '';
		}

		$query = " WHERE l.TipoLancamento IN (" . $typeList . ")";
		$query .= $this->buildCarteiraCondition($carteiraCodes, $carteiraMode);
		$query .= $this->buildUfCondition($ufCodes);
		$query .= " AND (DAY(l.DataHora_Evento) >= " . (int) $week['start'] . " AND DAY(l.DataHora_Evento) <= " . (int) $week['end'] . ")";
		$query .= " AND MONTH(l.DataHora_Evento) = " . (int) $month;
		$query .= " AND YEAR(l.DataHora_Evento) = " . (int) $year;

		return $query;
	}

	private function buildCarteiraCondition(array $carteiraCodes, $carteiraMode)
	{
		if (empty($carteiraCodes)) {
			return " AND 1 = 0";
		}

		$quotedCodes = $this->buildQuotedList($carteiraCodes);
		if ($carteiraMode === 'LIKE') {
			return " AND p.Carteira LIKE '%" . str_replace("'", '', implode(',', $carteiraCodes)) . "%'";
		}

		return " AND p.Carteira IN (" . $quotedCodes . ")";
	}

	private function buildUfCondition(array $ufCodes)
	{
		$quotedCodes = $this->buildQuotedList($ufCodes);
		if ($quotedCodes === '') {
			return '';
		}

		return " AND p.UFComarca IN (" . $quotedCodes . ")";
	}

	private function buildQuotedList(array $values)
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
}
