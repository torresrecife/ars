<?php

declare(strict_types=1);

namespace App\Repositories;

class SqlsrvLookupRepository extends NeoSqlsrvRepository
{
	public function isAvailable()
	{
		return $this->connection !== null;
	}

	public function listTipoAndamentoProcesso()
	{
		if (!$this->isAvailable()) {
			return array();
		}

		$query = "SELECT t.TIAP_Descricao
			FROM Tipo_Andamento_Processo AS t WITH (NOLOCK)
			WHERE t.TIAP_Descricao IS NOT NULL
			  AND ISNULL(t.TIAP_Inativo, 0) = 0
			  AND ISNULL(t.TIAP_Excluido, 0) = 0
			GROUP BY t.TIAP_Descricao
			ORDER BY t.TIAP_Descricao ASC";

		return $this->pluckNonEmpty($this->fetchAll($query), 'TIAP_Descricao');
	}

	public function listHonorTipoLancamento()
	{
		if (!$this->isAvailable()) {
			return array();
		}

		$query = "SELECT l.TipoLancamento
			FROM v_Lancamento_Processo AS l WITH (NOLOCK)
			WHERE l.ClassicaoLancamento LIKE 'Honor%'
			  AND l.TipoLancamento IS NOT NULL
			GROUP BY l.TipoLancamento
			ORDER BY l.TipoLancamento ASC";

		return $this->pluckNonEmpty($this->fetchAll($query), 'TipoLancamento');
	}

	public function listCarteiras()
	{
		if (!$this->isAvailable()) {
			return array();
		}

		$query = "SELECT c.CART_Descricao AS Carteira
			FROM Carteira AS c WITH (NOLOCK)
			WHERE c.CART_Descricao IS NOT NULL
			  AND LTRIM(RTRIM(c.CART_Descricao)) <> ''
			GROUP BY c.CART_Descricao
			ORDER BY c.CART_Descricao";

		return $this->pluckNonEmpty($this->fetchAll($query), 'Carteira');
	}

	private function pluckNonEmpty(array $rows, $field)
	{
		$values = array();
		foreach ($rows as $row) {
			$value = isset($row[$field]) ? trim((string) $row[$field]) : '';
			if ($value !== '') {
				$values[] = $value;
			}
		}

		return $values;
	}
}
