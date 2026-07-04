<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\RegionRepository;

class RegionService
{
	/** @var RegionRepository */
	private $repository;

	public function __construct(RegionRepository $repository)
	{
		$this->repository = $repository;
	}

	public function listActive()
	{
		return $this->repository->listActiveRegions();
	}

	public function listUserRegionIds($userId)
	{
		return $this->repository->listRegionIdsByUserId($userId);
	}

	public function listUserRegions($userId)
	{
		$ids = $this->listUserRegionIds($userId);
		if (empty($ids)) {
			return array();
		}

		return $this->repository->listRegionsByIds($ids);
	}

	public function listUserUfs($userId)
	{
		return $this->repository->listUfCodesByUserId($userId);
	}

	public function listUfsByRegionIds(array $regionIds)
	{
		return $this->repository->listUfCodesByRegionIds($regionIds);
	}

	public function syncUserRegions($userId, array $regionIds)
	{
		return $this->repository->replaceUserRegions($userId, $regionIds);
	}

	public function findUserRegion($userId, $regionId)
	{
		$regionId = (int) $regionId;
		if ($regionId <= 0) {
			return null;
		}

		foreach ($this->listUserRegions($userId) as $region) {
			if ((int) $region['regiao_id'] === $regionId) {
				return $region;
			}
		}

		return null;
	}
}
