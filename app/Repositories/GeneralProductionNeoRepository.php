<?php

declare(strict_types=1);

namespace App\Repositories;

class GeneralProductionNeoRepository extends NeoSqlsrvRepository
{
	public function sumFinancialByMonth(array $typeNames, array $carteiraCodes, $carteiraMode, $month, $year, array $ufCodes = array())
	{
		if ($this->connection === null) {
			return array('total' => 0.0, 'codes' => array());
		}

		$typeList = $this->buildQuotedList($typeNames);
		if ($typeList === '') {
			return array('total' => 0.0, 'codes' => array());
		}

		$query = '
			SELECT l.CodigoLancamento, l.Valor
			' . $this->financialMonthFromJoin()
			. ' WHERE l.TipoLancamento IN (' . $typeList . ')';
		$query .= $this->buildFinancialBaseConditions($carteiraCodes, $carteiraMode, $ufCodes, 'l.DataHora_Evento', $month, $year);
		$query .= $this->buildFinancialStatusCondition();
		$query .= ' GROUP BY l.CodigoLancamento, l.Valor';

		return $this->sumQuery($query, 'Valor', 'CodigoLancamento');
	}

	public function sumFinancialByWeek(array $typeNames, array $carteiraCodes, $carteiraMode, array $week, $month, $year, array $ufCodes = array())
	{
		if ($this->connection === null) {
			return array('total' => 0.0, 'codes' => array());
		}

		$typeList = $this->buildQuotedList($typeNames);
		if ($typeList === '') {
			return array('total' => 0.0, 'codes' => array());
		}

		$query = '
			SELECT l.CodigoLancamento, l.Valor
			' . $this->financialWeekFromJoin()
			. ' WHERE l.TipoLancamento IN (' . $typeList . ')';
		$query .= $this->buildFinancialBaseConditions($carteiraCodes, $carteiraMode, $ufCodes, 'l.DataHora_Evento', $month, $year, $week);
		$query .= ' GROUP BY l.CodigoLancamento, l.Valor';

		return $this->sumQuery($query, 'Valor', 'CodigoLancamento');
	}

	private function financialMonthFromJoin()
	{
		return "
			FROM Processo AS p WITH (NOLOCK)
			JOIN Lancamento_Processo AS l WITH (NOLOCK) ON l.CodigoProcesso = p.CodigoProcesso
		";
	}

	private function financialWeekFromJoin()
	{
		return "
			FROM v_Processo AS p WITH (NOLOCK)
			JOIN v_Lancamento_Processo AS l WITH (NOLOCK) ON l.CodigoProcesso = p.CodigoProcesso
		";
	}

	private function buildFinancialBaseConditions(array $carteiraCodes, $carteiraMode, array $ufCodes, $dateField, $month, $year, array $week = array())
	{
		$query = $this->buildCarteiraCondition($carteiraCodes, $carteiraMode, 'p.Carteira');
		$query .= $this->buildUfCondition($ufCodes, 'p.UFComarca');
		$query .= empty($week)
			? $this->buildMonthYearCondition($dateField, $month, $year)
			: $this->buildWeekCondition($dateField, $month, $year, $week);

		return $query;
	}

	private function buildFinancialStatusCondition()
	{
		return " AND l.StatusLancamento IN ('Pago pela Assessoria','Pendente na Assessoria','Enviado ao Contratante','Aprovado pelo Contratante','Pago pelo Contratante')";
	}
}
