<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\NeoDetailRepository;

class NeoDetailService
{
	/** @var NeoDetailRepository */
	private $repository;

	public function __construct(NeoDetailRepository $repository)
	{
		$this->repository = $repository;
	}

	public function financialDetailViewData(array $input)
	{
		$codes = $this->parseCodes(isset($input['codig_lnc']) ? $input['codig_lnc'] : '');
		$rows = $this->repository->financialDetails($codes);
		$total = 0.0;
		$count = 0;

		foreach ($rows as &$row) {
			$row['comarca_exibicao'] = $this->formatComarca(isset($row['comarca']) ? $row['comarca'] : '');
			$row['estado_exibicao'] = isset($row['estado']) ? trim((string) $row['estado']) : '';
			$total += isset($row['valores']) ? (float) $row['valores'] : 0.0;
			$count++;
		}
		unset($row);

		return array(
			'rows' => $rows,
			'bankName' => isset($input['banco_lnc']) ? (string) $input['banco_lnc'] : '',
			'totalCount' => $count,
			'totalValue' => $total,
		);
	}

	public function andamentoDetailViewData(array $input)
	{
		$codes = $this->parseCodes(isset($input['codig_and']) ? $input['codig_and'] : '');
		$rows = $this->repository->andamentoDetails($codes);
		$count = 0;

		foreach ($rows as &$row) {
			$row['comarca_exibicao'] = $this->formatComarca(isset($row['comarca']) ? $row['comarca'] : '');
			$row['estado_exibicao'] = isset($row['estado']) ? trim((string) $row['estado']) : '';
			$count++;
		}
		unset($row);

		return array(
			'rows' => $rows,
			'bankName' => isset($input['banco_and']) ? (string) $input['banco_and'] : '',
			'totalCount' => $count,
		);
	}

	private function parseCodes($value)
	{
		$codes = array();
		foreach (explode(',', (string) $value) as $code) {
			$code = trim($code);
			if ($code !== '' && ctype_digit($code)) {
				$codes[] = (int) $code;
			}
		}

		return $codes;
	}

	private function formatComarca($value)
	{
		$value = trim((string) $value);
		if ($value === '') {
			return '';
		}

		return htmlentities($value, ENT_QUOTES, 'UTF-8');
	}
}
