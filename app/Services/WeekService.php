<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\WeekRepository;
use App\Support\WriteResult;

class WeekService
{
	/** @var WeekRepository */
	private $repository;

	public function __construct(WeekRepository $repository)
	{
		$this->repository = $repository;
	}

	public function all()
	{
		$search = trim((string) request()->query('q', ''));

		return array(
			'weeks' => $this->repository->paginate(20, $search),
			'search' => $search,
		);
	}

	public function findById($weekId)
	{
		return $this->repository->findById($weekId);
	}

	public function formData(array $values = array())
	{
		return array(
			'week' => array(
				'semanas_id' => isset($values['semanas_id']) ? (int) $values['semanas_id'] : 0,
				'mes' => isset($values['mes']) ? (int) $values['mes'] : 0,
				'ano' => isset($values['ano']) ? (string) $values['ano'] : '',
				'ini_1' => isset($values['ini_1']) ? (int) $values['ini_1'] : 0,
				'fim_1' => isset($values['fim_1']) ? (int) $values['fim_1'] : 0,
				'ini_2' => isset($values['ini_2']) ? (int) $values['ini_2'] : 0,
				'fim_2' => isset($values['fim_2']) ? (int) $values['fim_2'] : 0,
				'ini_3' => isset($values['ini_3']) ? (int) $values['ini_3'] : 0,
				'fim_3' => isset($values['fim_3']) ? (int) $values['fim_3'] : 0,
				'ini_4' => isset($values['ini_4']) ? (int) $values['ini_4'] : 0,
				'fim_4' => isset($values['fim_4']) ? (int) $values['fim_4'] : 0,
				'ini_5' => isset($values['ini_5']) ? (int) $values['ini_5'] : 0,
				'fim_5' => isset($values['fim_5']) ? (int) $values['fim_5'] : 0,
			),
		);
	}

	public function createFromRequest(array $input)
	{
		$data = $this->normalizeWeekPayload($input);
		if ($this->repository->existsByMonthYear($data['mes'], $data['ano'])) {
			return WriteResult::duplicate();
		}

		return $this->repository->insert($data) ? WriteResult::success() : WriteResult::error();
	}

	public function updateFromRequest(array $input)
	{
		$weekId = isset($input['id_sem']) ? (int) $input['id_sem'] : 0;
		$data = $this->normalizeWeekPayload($input);

		if ($this->repository->existsByMonthYear($data['mes'], $data['ano'], $weekId)) {
			return WriteResult::duplicate();
		}

		return $this->repository->update($weekId, $data) ? WriteResult::success() : WriteResult::error();
	}

	public function delete($weekId)
	{
		return $this->repository->delete($weekId) ? WriteResult::success() : WriteResult::error();
	}

	private function normalizeWeekPayload(array $input)
	{
		$now = date('Y-m-d H:i:s');

		return array(
			'mes' => isset($input['mes_sem']) ? (int) $input['mes_sem'] : 0,
			'ano' => isset($input['ano_sem']) ? (int) preg_replace('/\D+/', '', (string) $input['ano_sem']) : 0,
			'ini_1' => isset($input['ini1_sem']) ? (int) $input['ini1_sem'] : 0,
			'fim_1' => isset($input['fim1_sem']) ? (int) $input['fim1_sem'] : 0,
			'ini_2' => isset($input['ini2_sem']) ? (int) $input['ini2_sem'] : 0,
			'fim_2' => isset($input['fim2_sem']) ? (int) $input['fim2_sem'] : 0,
			'ini_3' => isset($input['ini3_sem']) ? (int) $input['ini3_sem'] : 0,
			'fim_3' => isset($input['fim3_sem']) ? (int) $input['fim3_sem'] : 0,
			'ini_4' => isset($input['ini4_sem']) ? (int) $input['ini4_sem'] : 0,
			'fim_4' => isset($input['fim4_sem']) ? (int) $input['fim4_sem'] : 0,
			'ini_5' => !empty($input['ini5_sem']) ? (int) $input['ini5_sem'] : 0,
			'fim_5' => !empty($input['fim5_sem']) ? (int) $input['fim5_sem'] : 0,
			'data_cad' => $now,
			'data_arlt' => $now,
		);
	}
}
