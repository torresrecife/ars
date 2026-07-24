<?php

declare(strict_types=1);

namespace App\Repositories;

class NeoDetailRepository extends NeoSqlsrvRepository
{
	public function financialDetails(array $codes)
	{
		$codeList = $this->buildIntList($codes);
		if ($codeList === '') {
			return array();
		}

		$query = "
			SELECT
				" . $this->financialProcessColumns() . "
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
				" . $this->andamentoProcessColumns() . "
			FROM v_Processo AS p WITH (NOLOCK)
			JOIN v_Andamento_Processo AS a WITH (NOLOCK) ON a.CodigoProcesso = p.CodigoProcesso
			" . $this->ajuizamentoApply() . "
			WHERE a.CodigoAndamento IN (" . $codeList . ")
			AND " . $this->minimalContratoCondition() . "
			ORDER BY a.DataHoraEvento ASC
		";

		return $this->fetchAll($query);
	}

	public function financialDetailsByContext(array $typeNames, array $carteiraCodes, $carteiraMode, $month, $year, array $week = array(), array $ufCodes = array())
	{
		$typeList = $this->buildQuotedList($typeNames);
		if ($typeList === '') {
			return array();
		}

		$query = "
			SELECT
				" . $this->financialProcessColumns() . "
			FROM v_Processo AS p WITH (NOLOCK)
			JOIN v_Lancamento_Processo AS l WITH (NOLOCK) ON l.CodigoProcesso = p.CodigoProcesso
			WHERE l.TipoLancamento IN (" . $typeList . ")
		";

		$query .= $this->buildFinancialBaseConditions($carteiraCodes, $carteiraMode, $ufCodes, $month, $year, $week);
		$query .= ' ORDER BY l.DataHora_Evento ASC';

		return $this->fetchAll($query);
	}

	public function andamentoDetailsByContext(array $typeNames, array $carteiraCodes, $carteiraMode, $month, $year, array $week = array(), array $ufCodes = array())
	{
		$typeList = $this->buildQuotedList($typeNames);
		if ($typeList === '') {
			return array();
		}

		$query = "
			SELECT
				" . $this->andamentoProcessColumns() . "
			FROM v_Processo AS p WITH (NOLOCK)
			JOIN v_Andamento_Processo AS a WITH (NOLOCK) ON a.CodigoProcesso = p.CodigoProcesso
			" . $this->ajuizamentoApply() . "
			WHERE a.TipoAndamentoProcesso IN (" . $typeList . ")
			AND p.TipoProcesso NOT IN (N'CARTA PRECATÓRIA')
		";

		$query .= $this->buildAndamentoBaseConditions($carteiraCodes, $carteiraMode, $ufCodes, $month, $year, $week);
		$query .= ' ORDER BY a.DataHoraEvento ASC';

		return $this->fetchAll($query);
	}

	private function financialProcessColumns()
	{
		return "
			p.CodigoProcesso as Codigo,
			p.Comarca as comarca,
			p.UFComarca as estado,
			p.Cartorio as Cartorio,
			l.CodigoLancamento as CodigoLancamento,
			p.IdentificadorContratante as IdentificadorContratante,
			" . $this->parteSubquery($this->reuLabel(), 'Adverso') . ",
			" . $this->parteSubquery('Autor', 'Adverso2') . ",
			p.Area,
			p.NumeroProcesso as Processo,
			p.NumeroProcessoCNJ as ProcessoCNJ,
			p.ContaContratoNeoCobranca as ContaContratoNeoCobranca,
			l.TipoLancamento as Andamento,
			l.Valor as valores,
			FORMAT(l.DataHora_Evento, 'dd/MM/yyyy', 'en-US') as DataEvento,
			FORMAT(l.DataHora_Evento, 'dd/MM/yyyy', 'en-US') as DataCadastro
		";
	}

	private function andamentoProcessColumns()
	{
		return "
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
			" . $this->parteSubquery($this->reuLabel(), 'Adverso') . ",
			dist.DataAjuizamento as Ajuizamento
		";
	}

	private function parteSubquery($tipoPessoa, $alias)
	{
		return "(
			select top 1 pp.Pessoa
			from v_Parte_Processo as pp WITH (NOLOCK)
			where pp.TipoPessoa = N'" . str_replace("'", "''", (string) $tipoPessoa) . "'
			and pp.CodigoProcesso = p.CodigoProcesso
		) as " . $alias;
	}

	private function ajuizamentoApply()
	{
		return "
			OUTER APPLY (
				SELECT TOP 1 FORMAT(x.DataHoraEvento, 'dd/MM/yyyy', 'en-US') as DataAjuizamento
				FROM (
					SELECT ap.DataHoraEvento
					FROM v_Andamento_Processo as ap WITH (NOLOCK)
					WHERE ap.CodigoProcesso = p.CodigoProcesso
					AND ap.TipoAndamentoProcesso = N'" . $this->acaoDistribuidaLabel() . "'
					UNION ALL
					SELECT ah.DataHoraEvento
					FROM v_Andamento_Processo_Historico as ah WITH (NOLOCK)
					WHERE ah.CodigoProcesso = p.CodigoProcesso
					AND ah.TipoAndamentoProcesso = N'" . $this->acaoDistribuidaLabel() . "'
				) x
				ORDER BY x.DataHoraEvento DESC
			) dist
		";
	}

	private function minimalContratoCondition()
	{
		return "
			p.NumeroContratoNeoCobranca = (
				SELECT MIN(p2.NumeroContratoNeoCobranca)
				FROM v_Processo AS p2 WITH (NOLOCK)
				WHERE p2.CodigoProcesso = p.CodigoProcesso
			)
		";
	}

	private function buildFinancialBaseConditions(array $carteiraCodes, $carteiraMode, array $ufCodes, $month, $year, array $week = array())
	{
		$query = $this->buildCarteiraCondition($carteiraCodes, $carteiraMode);
		$query .= $this->buildUfCondition($ufCodes);
		$query .= $this->buildWeekCondition('l.DataHora_Evento', $month, $year, $week);

		return $query;
	}

	private function buildAndamentoBaseConditions(array $carteiraCodes, $carteiraMode, array $ufCodes, $month, $year, array $week = array())
	{
		$query = $this->buildCarteiraCondition($carteiraCodes, $carteiraMode);
		$query .= $this->buildUfCondition($ufCodes);
		$query .= $this->buildWeekCondition('a.DataHoraEvento', $month, $year, $week);
		$query .= " AND p.TipoDesdobramento IS NULL AND a.Invalido = 'False'";

		return $query;
	}

	private function reuLabel()
	{
		return json_decode('"\u0052\u00e9\u0075"');
	}

	private function acaoDistribuidaLabel()
	{
		return json_decode('"\u0041\u00e7\u00e3\u006f\u0020\u0064\u0069\u0073\u0074\u0072\u0069\u0062\u0075\u00ed\u0064\u0061"');
	}
}
