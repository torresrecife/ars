<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\SectorAdminRepository;

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
		return array(
			'areas' => $this->repository->listAll(),
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

	public function create(array $input)
	{
		$name = isset($input['area_nome']) ? trim((string) $input['area_nome']) : '';
		if ($name === '') {
			return '0';
		}

		if ($this->repository->existsByName($name)) {
			return '2';
		}

		return $this->repository->insert($name) ? '1' : '0';
	}

	public function update(array $input)
	{
		$areaId = isset($input['area_id']) ? (int) $input['area_id'] : 0;
		$name = isset($input['area_nome']) ? trim((string) $input['area_nome']) : '';
		if ($areaId <= 0 || $name === '') {
			return '0';
		}

		if ($this->repository->existsByName($name, $areaId)) {
			return '2';
		}

		return $this->repository->update($areaId, $name) ? '1' : '0';
	}

	public function delete($areaId)
	{
		return $this->repository->delete($areaId) ? '1' : '0';
	}
}
