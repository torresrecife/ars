<?php

declare(strict_types=1);

namespace App\Repositories;

class NeoDetailRepository
{
	/** @var mixed */
	private $connection;

	public function __construct($connection)
	{
		$this->connection = $connection;
	}

	public function financialDetails(array $codes)
	{
		$codeList = $this->buildIntList($codes);
		if ($codeList === '') {
			return array();
		}

		$query = "
			SELECT
				p.CodigoProcesso as Codigo,
				p.Comarca as comarca,
				p.UFComarca as estado,
				(
					select top 1 pp.Pessoa
					from v_Parte_Processo as pp WITH (NOLOCK)
					where pp.TipoPessoa='RÃ©u' and pp.CodigoProcesso=p.CodigoProcesso
				) as Adverso,
				(
					select top 1 pp.Pessoa
					from v_Parte_Processo as pp WITH (NOLOCK)
					where pp.TipoPessoa='Autor' and pp.CodigoProcesso=p.CodigoProcesso
				) as Adverso2,
				p.Area,
				p.NumeroProcessoCNJ as Processo,
				p.ContaContratoNeoCobranca as ContaContratoNeoCobranca,
				l.TipoLancamento as Andamento,
				l.Valor as valores,
				FORMAT(l.DataHora_Evento, 'dd/MM/yyyy', 'en-US') as DataEvento,
				FORMAT(l.DataHora_Evento, 'dd/MM/yyyy', 'en-US') as DataCadastro
			FROM v_Processo AS p WITH (NOLOCK)
			JOIN v_Lancamento_Processo AS l WITH (NOLOCK) ON l.CodigoProcesso = p.CodigoProcesso
			WHERE l.CodigoLancamento IN (" . $codeList . ")
			ORDER BY l.DataHora_Evento ASC
		";

		return $this->fetchAll($query);
	}

	public function andamentoDetails(array $codes)
	{
		$codeList = $this->buildIntList($codes);
		if ($codeList === '') {
			return array();
		}

		$query = "
			SELECT
				a.DataHoraEvento,
				a.CodigoAndamento,
				p.CodigoProcesso as Codigo,
				p.Comarca as comarca,
				p.UFComarca as estado,
				p.NumeroProcessoCNJ as Processo,
				p.ContaContratoNeoCobranca as ContaContratoNeoCobranca,
				a.TipoAndamentoProcesso as Andamento,
				FORMAT(a.DataHoraEvento, 'dd/MM/yyyy', 'en-US') as DataEvento,
				FORMAT(a.DataHora, 'dd/MM/yyyy', 'en-US') as DataCadastro,
				(
					select top 1 pp.Pessoa
					from v_Parte_Processo as pp WITH (NOLOCK)
					where pp.TipoPessoa='RÃ©u' and pp.CodigoProcesso=p.CodigoProcesso
				) as Adverso,
				dist.DataAjuizamento as Ajuizamento
			FROM v_Processo AS p WITH (NOLOCK)
			JOIN v_Andamento_Processo AS a WITH (NOLOCK) ON a.CodigoProcesso = p.CodigoProcesso
			OUTER APPLY (
				SELECT TOP 1 FORMAT(x.DataHoraEvento, 'dd/MM/yyyy', 'en-US') as DataAjuizamento
				FROM (
					SELECT ap.DataHoraEvento
					FROM v_Andamento_Processo as ap WITH (NOLOCK)
					WHERE ap.CodigoProcesso = p.CodigoProcesso
					AND ap.TipoAndamentoProcesso = 'AÃ§Ã£o distribuÃ­da'
					UNION ALL
					SELECT ah.DataHoraEvento
					FROM v_Andamento_Processo_Historico as ah WITH (NOLOCK)
					WHERE ah.CodigoProcesso = p.CodigoProcesso
					AND ah.TipoAndamentoProcesso = 'AÃ§Ã£o distribuÃ­da'
				) x
				ORDER BY x.DataHoraEvento DESC
			) dist
			WHERE a.CodigoAndamento IN (" . $codeList . ")
			AND p.NumeroContratoNeoCobranca = (
				SELECT MIN(p2.NumeroContratoNeoCobranca)
				FROM v_Processo AS p2 WITH (NOLOCK)
				WHERE p2.CodigoProcesso = p.CodigoProcesso
			)
			ORDER BY a.DataHoraEvento ASC
		";

		return $this->fetchAll($query);
	}

	public function financialDetailsByContext(array $typeNames, array $carteiraCodes, $carteiraMode, $month, $year, array $week = array())
	{
		$typeList = $this->buildQuotedList($typeNames);
		if ($typeList === '') {
			return array();
		}

		$query = "
			SELECT
				p.CodigoProcesso as Codigo,
				p.Comarca as comarca,
				p.UFComarca as estado,
				(
					select top 1 pp.Pessoa
					from v_Parte_Processo as pp WITH (NOLOCK)
					where pp.TipoPessoa='RÃ©u' and pp.CodigoProcesso=p.CodigoProcesso
				) as Adverso,
				(
					select top 1 pp.Pessoa
					from v_Parte_Processo as pp WITH (NOLOCK)
					where pp.TipoPessoa='Autor' and pp.CodigoProcesso=p.CodigoProcesso
				) as Adverso2,
				p.Area,
				p.NumeroProcessoCNJ as Processo,
				p.ContaContratoNeoCobranca as ContaContratoNeoCobranca,
				l.TipoLancamento as Andamento,
				l.Valor as valores,
				FORMAT(l.DataHora_Evento, 'dd/MM/yyyy', 'en-US') as DataEvento,
				FORMAT(l.DataHora_Evento, 'dd/MM/yyyy', 'en-US') as DataCadastro
			FROM v_Processo AS p WITH (NOLOCK)
			JOIN v_Lancamento_Processo AS l WITH (NOLOCK) ON l.CodigoProcesso = p.CodigoProcesso
			WHERE l.TipoLancamento IN (" . $typeList . ")
		";

		$query .= $this->buildCarteiraCondition($carteiraCodes, $carteiraMode);
		$query .= $this->buildWeekCondition('l.DataHora_Evento', $month, $year, $week);
		$query .= " ORDER BY l.DataHora_Evento ASC";

		return $this->fetchAll($query);
	}

	public function andamentoDetailsByContext(array $typeNames, array $carteiraCodes, $carteiraMode, $month, $year, array $week = array())
	{
		$typeList = $this->buildQuotedList($typeNames);
		if ($typeList === '') {
			return array();
		}

		$query = "
			SELECT
				a.DataHoraEvento,
				a.CodigoAndamento,
				p.CodigoProcesso as Codigo,
				p.Comarca as comarca,
				p.UFComarca as estado,
				p.NumeroProcessoCNJ as Processo,
				p.ContaContratoNeoCobranca as ContaContratoNeoCobranca,
				a.TipoAndamentoProcesso as Andamento,
				FORMAT(a.DataHoraEvento, 'dd/MM/yyyy', 'en-US') as DataEvento,
				FORMAT(a.DataHora, 'dd/MM/yyyy', 'en-US') as DataCadastro,
				(
					select top 1 pp.Pessoa
					from v_Parte_Processo as pp WITH (NOLOCK)
					where pp.TipoPessoa='RÃ©u' and pp.CodigoProcesso=p.CodigoProcesso
				) as Adverso,
				dist.DataAjuizamento as Ajuizamento
			FROM v_Processo AS p WITH (NOLOCK)
			JOIN v_Andamento_Processo AS a WITH (NOLOCK) ON a.CodigoProcesso = p.CodigoProcesso
			OUTER APPLY (
				SELECT TOP 1 FORMAT(x.DataHoraEvento, 'dd/MM/yyyy', 'en-US') as DataAjuizamento
				FROM (
					SELECT ap.DataHoraEvento
					FROM v_Andamento_Processo as ap WITH (NOLOCK)
					WHERE ap.CodigoProcesso = p.CodigoProcesso
					AND ap.TipoAndamentoProcesso = 'AÃ§Ã£o distribuÃ­da'
					UNION ALL
					SELECT ah.DataHoraEvento
					FROM v_Andamento_Processo_Historico as ah WITH (NOLOCK)
					WHERE ah.CodigoProcesso = p.CodigoProcesso
					AND ah.TipoAndamentoProcesso = 'AÃ§Ã£o distribuÃ­da'
				) x
				ORDER BY x.DataHoraEvento DESC
			) dist
			WHERE a.TipoAndamentoProcesso IN (" . $typeList . ")
			AND p.TipoProcesso NOT IN (N'CARTA PRECATÃ“RIA')
		";

		$query .= $this->buildCarteiraCondition($carteiraCodes, $carteiraMode);
		$query .= $this->buildWeekCondition('a.DataHoraEvento', $month, $year, $week);
		$query .= " AND p.TipoDesdobramento IS NULL AND a.Invalido = 'False'";
		$query .= " ORDER BY a.DataHoraEvento ASC";

		return $this->fetchAll($query);
	}

	private function buildIntList(array $codes)
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

	private function buildCarteiraCondition(array $carteiraCodes, $carteiraMode)
	{
		if (empty($carteiraCodes)) {
			return " AND 1 = 0";
		}

		if ($carteiraMode === 'LIKE') {
			return " AND p.Carteira LIKE '%" . str_replace("'", '', implode(',', $carteiraCodes)) . "%'";
		}

		return " AND p.Carteira IN (" . $this->buildQuotedList($carteiraCodes) . ")";
	}

	private function buildWeekCondition($field, $month, $year, array $week)
	{
		$query = " AND MONTH(" . $field . ") = " . (int) $month;
		$query .= " AND YEAR(" . $field . ") = " . (int) $year;

		if (isset($week['start'], $week['end'])) {
			$query .= " AND DAY(" . $field . ") >= " . (int) $week['start'];
			$query .= " AND DAY(" . $field . ") <= " . (int) $week['end'];
		}

		return $query;
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

	private function fetchAll($query)
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
}
