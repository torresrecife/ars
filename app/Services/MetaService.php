<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\MetaRepository;
use App\Services\RegionService;

class MetaService
{
	/** @var MetaRepository */
	private $repository;

	/** @var RegionService */
	private $regionService;

	public function __construct(MetaRepository $repository, RegionService $regionService)
	{
		$this->repository = $repository;
		$this->regionService = $regionService;
	}

	public function getBank($bankId)
	{
		return $this->repository->findBankById($bankId);
	}

	public function listByBankMonthYear($bankId, $month, $year)
	{
		return $this->repository->listByBankMonthYear($bankId, $month, $year);
	}

	public function listAndamentos()
	{
		return $this->repository->listAndamentos();
	}

	public function findById($metaId)
	{
		return $this->repository->findById($metaId);
	}

	public function listRegions()
	{
		return $this->regionService->listActive();
	}

	public function createManyFromRequest(array $input)
	{
		$total = isset($input['numes']) ? (int) $input['numes'] : 0;
		for ($index = 1; $index <= $total; $index++) {
			$data = $this->extractMetaPayload($input, $index);
			if (!$this->repository->insert($data)) {
				return false;
			}
		}

		return true;
	}

	public function updateManyFromRequest(array $input)
	{
		$total = isset($input['numes']) ? (int) $input['numes'] : 0;
		$metaId = isset($input['meta_id']) ? (int) $input['meta_id'] : 0;

		for ($index = 1; $index <= $total; $index++) {
			$data = $this->extractMetaPayload($input, $index);
			if (!$this->repository->update($metaId, $data)) {
				return false;
			}
		}

		return true;
	}

	public function delete($metaId)
	{
		return $this->repository->delete($metaId);
	}

	public function totalFinancialMeta(array $metas)
	{
		$total = 0.0;
		foreach ($metas as $meta) {
			if ((int) $meta['especie'] === 2) {
				$total += (float) $meta['meta_valor'];
			}
		}

		return $total;
	}

	private function extractMetaPayload(array $input, $index)
	{
		$defSem = !empty($input['def_sem_' . $index]) ? 'Y' : 'N';
		$sem1 = $this->parseNullableDecimal(isset($input['sem1_valor_' . $index]) ? $input['sem1_valor_' . $index] : null);
		$sem2 = $this->parseNullableDecimal(isset($input['sem2_valor_' . $index]) ? $input['sem2_valor_' . $index] : null);
		$sem3 = $this->parseNullableDecimal(isset($input['sem3_valor_' . $index]) ? $input['sem3_valor_' . $index] : null);
		$sem4 = $this->parseNullableDecimal(isset($input['sem4_valor_' . $index]) ? $input['sem4_valor_' . $index] : null);
		$sem5 = $this->parseNullableDecimal(isset($input['sem5_valor_' . $index]) ? $input['sem5_valor_' . $index] : null);
		$metaValor = (float) $this->parseDecimal(isset($input['meta_valor_' . $index]) ? $input['meta_valor_' . $index] : 0);

		if ($defSem === 'Y') {
			$metaValor = $this->sumWeekValues(array($sem1, $sem2, $sem3, $sem4, $sem5));
		}

		return array(
			'banco_id' => isset($input['banco_id']) ? (int) $input['banco_id'] : 0,
			'meta_mes' => isset($input['meta_mes']) ? (int) $input['meta_mes'] : 0,
			'meta_ano' => isset($input['meta_ano']) ? (int) $input['meta_ano'] : 0,
			'anda_id' => isset($input['meta_name_' . $index]) ? (int) $input['meta_name_' . $index] : 0,
			'regiao_id' => isset($input['regiao_id_' . $index]) && (int) $input['regiao_id_' . $index] > 0 ? (int) $input['regiao_id_' . $index] : null,
			'def_sem' => $defSem,
			'sem_1' => $sem1,
			'sem_2' => $sem2,
			'sem_3' => $sem3,
			'sem_4' => $sem4,
			'sem_5' => $sem5,
			'meta_valor' => $metaValor,
		);
	}

	private function parseDecimal($value)
	{
		$value = is_string($value) ? trim($value) : $value;
		return str_replace(',', '.', str_replace('.', '', (string) $value));
	}

	private function parseNullableDecimal($value)
	{
		if ($value === null || $value === '') {
			return null;
		}

		return (float) $this->parseDecimal($value);
	}

	private function sumWeekValues(array $values)
	{
		$total = 0.0;
		foreach ($values as $value) {
			$total += ($value === null) ? 0.0 : (float) $value;
		}

		return (float) number_format($total, 2, '.', '');
	}
}
