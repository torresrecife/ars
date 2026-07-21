<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\MetaRepository;
use App\Services\RegionService;
use App\Support\WriteResult;

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

	public function regionSelectionData(array $session = array())
	{
		$level = isset($session['usuarioNivel']) ? (string) $session['usuarioNivel'] : '';
		$mode = isset($session['usuarioRegiaoModo']) ? (string) $session['usuarioRegiaoModo'] : 'N';
		$userId = isset($session['usuarioID']) ? (int) $session['usuarioID'] : 0;

		if ($level === 'ADM') {
			return array(
				'regions' => $this->regionService->listActive(),
				'allowGlobal' => true,
			);
		}

		if ($userId > 0 && in_array($level, array('GER', 'USU'), true)) {
			$userRegions = $this->regionService->listUserRegions($userId);
			if (!empty($userRegions)) {
				return array(
					'regions' => $userRegions,
					'allowGlobal' => ($level === 'GER' && $mode === 'T'),
				);
			}
		}

		return array(
			'regions' => $this->regionService->listActive(),
			'allowGlobal' => true,
		);
	}

	public function createManyFromRequest(array $input)
	{
		$total = isset($input['numes']) ? (int) $input['numes'] : 0;
		$seen = array();
		for ($index = 1; $index <= $total; $index++) {
			$data = $this->extractMetaPayload($input, $index);
			$key = $this->duplicateKey($data);
			if (isset($seen[$key]) || $this->repository->existsDuplicate($data)) {
				return WriteResult::duplicate();
			}
			$seen[$key] = true;
			if (!$this->repository->insert($data)) {
				return WriteResult::error();
			}
		}

		return WriteResult::success();
	}

	public function updateManyFromRequest(array $input)
	{
		$total = isset($input['numes']) ? (int) $input['numes'] : 0;
		$metaId = isset($input['meta_id']) ? (int) $input['meta_id'] : 0;
		$seen = array();

		for ($index = 1; $index <= $total; $index++) {
			$data = $this->extractMetaPayload($input, $index);
			$key = $this->duplicateKey($data);
			if (isset($seen[$key]) || $this->repository->existsDuplicate($data, $metaId)) {
				return WriteResult::duplicate();
			}
			$seen[$key] = true;
			if (!$this->repository->update($metaId, $data)) {
				return WriteResult::error();
			}
		}

		return WriteResult::success();
	}

	public function delete($metaId)
	{
		return $this->repository->delete($metaId) ? WriteResult::success() : WriteResult::error();
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
		$defSemValue = isset($input['def_sem_' . $index]) ? strtoupper(trim((string) $input['def_sem_' . $index])) : 'N';
		$defSem = ($defSemValue === 'Y') ? 'Y' : 'N';
		$sem1 = $this->parseNullableDecimal(isset($input['sem1_valor_' . $index]) ? $input['sem1_valor_' . $index] : null);
		$sem2 = $this->parseNullableDecimal(isset($input['sem2_valor_' . $index]) ? $input['sem2_valor_' . $index] : null);
		$sem3 = $this->parseNullableDecimal(isset($input['sem3_valor_' . $index]) ? $input['sem3_valor_' . $index] : null);
		$sem4 = $this->parseNullableDecimal(isset($input['sem4_valor_' . $index]) ? $input['sem4_valor_' . $index] : null);
		$sem5 = $this->parseNullableDecimal(isset($input['sem5_valor_' . $index]) ? $input['sem5_valor_' . $index] : null);
		$metaValor = (float) $this->parseDecimal(isset($input['meta_valor_' . $index]) ? $input['meta_valor_' . $index] : 0);

		if ($defSem === 'Y') {
			$metaValor = $this->sumWeekValues(array($sem1, $sem2, $sem3, $sem4, $sem5));
		} elseif ($metaValor > 0 && $this->weeksAreEmpty(array($sem1, $sem2, $sem3, $sem4, $sem5))) {
			$sem1 = $metaValor;
			$sem2 = null;
			$sem3 = null;
			$sem4 = null;
			$sem5 = null;
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

	private function weeksAreEmpty(array $values)
	{
		foreach ($values as $value) {
			if ($value !== null && (float) $value != 0.0) {
				return false;
			}
		}

		return true;
	}

	private function duplicateKey(array $data)
	{
		return implode(':', array(
			(int) $data['banco_id'],
			(int) $data['meta_mes'],
			(int) $data['meta_ano'],
			(int) $data['anda_id'],
			isset($data['regiao_id']) && (int) $data['regiao_id'] > 0 ? (int) $data['regiao_id'] : 0,
		));
	}
}
