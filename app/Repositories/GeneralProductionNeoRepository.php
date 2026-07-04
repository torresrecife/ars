<?php

declare(strict_types=1);

namespace App\Repositories;

class GeneralProductionNeoRepository
{
	/** @var mixed */
	private $connection;

	public function __construct($connection)
	{
		$this->connection = $connection;
	}

	public function sumFinancialByMonth(array $typeNames, array $carteiraCodes, $carteiraMode, $month, $year, array $ufCodes = array())
	{
		if ($this->connection === null) {
			return array('total' => 0.0, 'codes' => array());
		}

		$typeList = $this->buildQuotedList($typeNames);
		if ($typeList === '') {
			return array('total' => 0.0, 'codes' => array());
		}

		$query = "
			SELECT l.CodigoLancamento, l.Valor
			FROM Processo AS p WITH (NOLOCK)
			JOIN Lancamento_Processo AS l WITH (NOLOCK) ON l.CodigoProcesso = p.CodigoProcesso
			WHERE l.TipoLancamento IN (" . $typeList . ")
		";
		$query .= $this->buildCarteiraCondition($carteiraCodes, $carteiraMode, 'p.Carteira');
		$query .= $this->buildUfCondition($ufCodes, 'p.UFComarca');
		$query .= " AND l.StatusLancamento IN ('Pago pela Assessoria','Pendente na Assessoria','Enviado ao Contratante','Aprovado pelo Contratante','Pago pelo Contratante')";
		$query .= " AND MONTH(l.DataHora_Evento) = " . (int) $month;
		$query .= " AND YEAR(l.DataHora_Evento) = " . (int) $year;
		$query .= " GROUP BY l.CodigoLancamento, l.Valor";

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

		$query = "
			SELECT l.CodigoLancamento, l.Valor
			FROM v_Processo AS p WITH (NOLOCK)
			JOIN v_Lancamento_Processo AS l WITH (NOLOCK) ON l.CodigoProcesso = p.CodigoProcesso
			WHERE l.TipoLancamento IN (" . $typeList . ")
		";
		$query .= $this->buildCarteiraCondition($carteiraCodes, $carteiraMode, 'p.Carteira');
		$query .= $this->buildUfCondition($ufCodes, 'p.UFComarca');
		$query .= " AND DAY(l.DataHora_Evento) >= " . (int) $week['start'];
		$query .= " AND DAY(l.DataHora_Evento) <= " . (int) $week['end'];
		$query .= " AND MONTH(l.DataHora_Evento) = " . (int) $month;
		$query .= " AND YEAR(l.DataHora_Evento) = " . (int) $year;
		$query .= " GROUP BY l.CodigoLancamento, l.Valor";

		return $this->sumQuery($query, 'Valor', 'CodigoLancamento');
	}

	private function sumQuery($query, $valueField, $codeField)
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

		return array('total' => $total, 'codes' => array_values($codes));
	}

	private function buildCarteiraCondition(array $carteiraCodes, $carteiraMode, $field)
	{
		if (empty($carteiraCodes)) {
			return " AND 1 = 0";
		}

		if ($carteiraMode === 'LIKE') {
			return " AND " . $field . " LIKE '%" . str_replace("'", '', implode(',', $carteiraCodes)) . "%'";
		}

		return " AND " . $field . " IN (" . $this->buildQuotedList($carteiraCodes) . ")";
	}

	private function buildUfCondition(array $ufCodes, $field)
	{
		$quoted = $this->buildQuotedList($ufCodes);
		if ($quoted === '') {
			return '';
		}

		return " AND " . $field . " IN (" . $quoted . ")";
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
