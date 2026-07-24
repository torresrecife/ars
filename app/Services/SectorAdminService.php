<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\SectorAdminRepository;
use App\Support\WriteResult;

class SectorAdminService
{
	/** @var SectorAdminRepository */
	private $repository;

	public function __construct(SectorAdminRepository $repository)
	{
		$this->repository = $repository;
	}

	public function indexData()
	{
		$search = trim((string) request()->query('q', ''));

		return array(
			'areas' => $this->repository->paginate(20, $search),
			'search' => $search,
		);
	}

	public function editPayload($areaId)
	{
		$row = $this->repository->findById($areaId);
		if (!$row) {
			return null;
		}

		return array(
			'area_id' => isset($row['area_id']) ? (int) $row['area_id'] : 0,
			'area_nome' => isset($row['area_nome']) ? (string) $row['area_nome'] : '',
			'area_date' => isset($row['area_date']) ? (string) $row['area_date'] : '',
		);
	}

	public function formData(array $values = array())
	{
		return array(
			'sector' => array(
				'area_id' => isset($values['area_id']) ? (int) $values['area_id'] : 0,
				'area_nome' => isset($values['area_nome']) ? (string) $values['area_nome'] : '',
			),
		);
	}

	public function create(array $input)
	{
		$name = isset($input['area_nome']) ? trim((string) $input['area_nome']) : '';
		if ($name === '') {
			return WriteResult::error();
		}

		if ($this->repository->existsByName($name)) {
			return WriteResult::duplicate();
		}

		return $this->repository->insert($name) ? WriteResult::success() : WriteResult::error();
	}

	public function update(array $input)
	{
		$areaId = isset($input['area_id']) ? (int) $input['area_id'] : 0;
		$name = isset($input['area_nome']) ? trim((string) $input['area_nome']) : '';
		if ($areaId <= 0 || $name === '') {
			return WriteResult::error();
		}

		if ($this->repository->existsByName($name, $areaId)) {
			return WriteResult::duplicate();
		}

		return $this->repository->update($areaId, $name) ? WriteResult::success() : WriteResult::error();
	}

	public function delete($areaId)
	{
		return $this->repository->delete($areaId) ? WriteResult::success() : WriteResult::error();
	}
}
