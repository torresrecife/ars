<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\WeekRepository;

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
		return $this->repository->all();
	}

	public function findById($weekId)
	{
		return $this->repository->findById($weekId);
	}

	public function createFromRequest(array $input)
	{
		$data = $this->normalizeWeekPayload($input);
		if ($this->repository->existsByMonthYear($data['mes'], $data['ano'])) {
			return 2;
		}

		return $this->repository->insert($data) ? 1 : 3;
	}

	public function updateFromRequest(array $input)
	{
		$weekId = isset($input['id_sem']) ? (int) $input['id_sem'] : 0;
		$data = $this->normalizeWeekPayload($input);

		if ($this->repository->existsByMonthYear($data['mes'], $data['ano'], $weekId)) {
			return 2;
		}

		return $this->repository->update($weekId, $data) ? 1 : 3;
	}

	public function delete($weekId)
	{
		return $this->repository->delete($weekId);
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
