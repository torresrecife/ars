<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Banco;
use App\Repositories\SqlsrvLookupRepository;
use Illuminate\Http\Request;

class SelectController extends Controller
{
	/** @var SqlsrvLookupRepository */
	private $sqlsrvLookupRepository;

	public function __construct(SqlsrvLookupRepository $sqlsrvLookupRepository)
	{
		$this->sqlsrvLookupRepository = $sqlsrvLookupRepository;
	}

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
		if ($flag === '1') {
			foreach ($this->sqlsrvLookupRepository->listTipoAndamentoProcesso() as $descricao) {
				$options[] = '<option value="' . $this->escape($descricao) . '"> ' . $this->escape($descricao) . '</option>';
			}
		} elseif ($flag === '2') {
			foreach ($this->sqlsrvLookupRepository->listHonorTipoLancamento() as $descricao) {
				$options[] = '<option value="' . $this->escape($descricao) . '"> ' . $this->escape($descricao) . '</option>';
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
