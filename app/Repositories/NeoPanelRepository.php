<?php

declare(strict_types=1);

namespace App\Repositories;

class NeoPanelRepository extends NeoSqlsrvRepository
{
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
		$codeResult = sqlsrv_query($this->connection, 'SELECT a.CodigoAndamento ' . $queryBase);
		while ($codeResult && ($row = sqlsrv_fetch_array($codeResult, SQLSRV_FETCH_ASSOC))) {
			$codes[] = $row['CodigoAndamento'];
		}

		$count = 0;
		$countResult = sqlsrv_query($this->connection, 'SELECT 1 as qtd ' . $queryBase);
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

		$query = '
			SELECT l.CodigoLancamento, l.Valor as qtd2
			' . $this->financialFromJoin();

		$where = $this->buildFinancialWhere($typeNames, $carteiraCodes, $carteiraMode, $week, $month, $year, $ufCodes);
		if ($where === '') {
			return array('total' => 0.0, 'codes' => array());
		}

		$query .= $where;
		$query .= ' GROUP BY l.CodigoLancamento, l.Valor';

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

		$query = $this->productionFromJoin();
		$query .= ' WHERE a.TipoAndamentoProcesso IN (' . $typeList . ')';
		$query .= $this->buildProductionBaseConditions($carteiraCodes, $carteiraMode, $week, $month, $year, $ufCodes);
		$query .= ' GROUP BY a.CodigoAndamento';

		return $query;
	}

	private function buildFinancialWhere(array $typeNames, array $carteiraCodes, $carteiraMode, array $week, $month, $year, array $ufCodes = array())
	{
		$typeList = $this->buildQuotedList($typeNames);
		if ($typeList === '') {
			return '';
		}

		$query = ' WHERE l.TipoLancamento IN (' . $typeList . ')';
		$query .= $this->buildFinancialBaseConditions($carteiraCodes, $carteiraMode, $week, $month, $year, $ufCodes);

		return $query;
	}

	private function productionFromJoin()
	{
		return "
			FROM v_Andamento_Processo AS a WITH (NOLOCK)
			JOIN v_Processo AS p WITH (NOLOCK) ON p.CodigoProcesso = a.CodigoProcesso
		";
	}

	private function financialFromJoin()
	{
		return "
			FROM v_Processo AS p WITH (NOLOCK)
			JOIN v_Lancamento_Processo AS l WITH (NOLOCK) ON l.CodigoProcesso = p.CodigoProcesso
		";
	}

	private function buildProductionBaseConditions(array $carteiraCodes, $carteiraMode, array $week, $month, $year, array $ufCodes = array())
	{
		$query = " AND p.TipoProcesso NOT IN (N'CARTA PRECATÃƒâ€œRIA')";
		$query .= $this->buildCarteiraCondition($carteiraCodes, $carteiraMode);
		$query .= $this->buildUfCondition($ufCodes);
		$query .= $this->buildWeekCondition('a.DataHoraEvento', $month, $year, $week);
		$query .= " AND p.TipoDesdobramento IS NULL AND a.Invalido = 'False'";

		return $query;
	}

	private function buildFinancialBaseConditions(array $carteiraCodes, $carteiraMode, array $week, $month, $year, array $ufCodes = array())
	{
		$query = $this->buildCarteiraCondition($carteiraCodes, $carteiraMode);
		$query .= $this->buildUfCondition($ufCodes);
		$query .= $this->buildWeekCondition('l.DataHora_Evento', $month, $year, $week);

		return $query;
	}
}
