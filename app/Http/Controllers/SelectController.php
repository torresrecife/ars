<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Banco;
use Illuminate\Http\Request;

class SelectController extends Controller
{
	public function webAjax(Request $request)
	{
		$options = array('<option value="">  </option>');
		$dados = (string) $request->input('dados', '');
		$flag = (string) $request->input('flag', '');

		if ($dados === '0') {
			$this->appendSqlsrvOptions($options, $flag);
		} elseif ($dados === '1') {
			$this->appendBankOptions($options, (int) $flag);
		}

		return response(implode('', $options), 200, array(
			'Content-Type' => 'text/html; charset=UTF-8',
		));
	}

	private function appendSqlsrvOptions(array &$options, $flag)
	{
		if (!function_exists('ars_sqlsrv_connection') || !function_exists('sqlsrv_query')) {
			return;
		}

		$conexao = ars_sqlsrv_connection();
		if (!$conexao) {
			return;
		}

		if ($flag === '1') {
			$query = "SELECT t.TIAP_Descricao FROM Tipo_Andamento_Processo AS t WITH (NOLOCK) WHERE t.TIAP_Descricao IS NOT NULL AND ISNULL(t.TIAP_Inativo, 0) = 0 AND ISNULL(t.TIAP_Excluido, 0) = 0 GROUP BY t.TIAP_Descricao ORDER BY t.TIAP_Descricao ASC";
			$result = sqlsrv_query($conexao, $query);
			if ($result) {
				while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
					$descricao = isset($row['TIAP_Descricao']) ? (string) $row['TIAP_Descricao'] : '';
					if ($descricao !== '') {
						$options[] = '<option value="' . $this->escape($descricao) . '"> ' . $this->escape($descricao) . '</option>';
					}
				}
			}
		} elseif ($flag === '2') {
			$query = "SELECT l.TipoLancamento FROM v_Lancamento_Processo AS l WITH (NOLOCK) WHERE l.ClassicaoLancamento LIKE 'Honor%' AND l.TipoLancamento IS NOT NULL GROUP BY l.TipoLancamento ORDER BY l.TipoLancamento ASC";
			$result = sqlsrv_query($conexao, $query);
			if ($result) {
				while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
					$descricao = isset($row['TipoLancamento']) ? (string) $row['TipoLancamento'] : '';
					if ($descricao !== '') {
						$options[] = '<option value="' . $this->escape($descricao) . '"> ' . $this->escape($descricao) . '</option>';
					}
				}
			}
		}
	}

	private function appendBankOptions(array &$options, $areaId)
	{
		$query = Banco::query()->orderBy('banco_name');
		if ($areaId > 0) {
			$query->where('banco_area', $areaId);
		}

		foreach ($query->get(array('banco_id', 'banco_name')) as $bank) {
			$options[] = '<option value="' . (int) $bank->getAttribute('banco_id') . '"> ' . $this->escape((string) $bank->getAttribute('banco_name')) . '</option>';
		}
	}

	private function escape($value)
	{
		return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
	}
}
