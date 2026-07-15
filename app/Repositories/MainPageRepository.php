<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Area;
use App\Models\Banco;

class MainPageRepository
{
	public function listAreas($userSectorId)
	{
		$query = Area::query()
			->select(array('area_id', 'area_nome'))
			->where('area_status', 'Y');

		if ((int) $userSectorId !== 0) {
			$query->where('area_id', (int) $userSectorId);
		}

		return $query->orderBy('area_nome')
			->get()
			->map(function (Area $area) {
				return $area->toArray();
			})
			->values()
			->all();
	}

	public function listBanksByArea($areaId, $userClientIds)
	{
		$query = Banco::query()
			->select(array('banco_id', 'banco_name', 'banco_class'))
			->where('banco_area', (int) $areaId)
			->whereIn('banco_status', array('Y', 'P'));

		$clientIds = $this->parseIdList($userClientIds);
		if (!empty($clientIds)) {
			$query->whereIn('banco_id', $clientIds);
		}

		return $query->orderBy('banco_area')
			->get()
			->map(function (Banco $banco) {
				return $banco->toArray();
			})
			->values()
			->all();
	}

	public function listAreasForProduction($userLevel, $userSectorId)
	{
		$query = Area::query()
			->select(array('area_id', 'area_nome'))
			->where('area_status', 'Y');

		if ((string) $userLevel !== 'ADM') {
			$query->where('area_id', (int) $userSectorId);
		}

		return $query->orderBy('area_nome')
			->get()
			->map(function (Area $area) {
				return $area->toArray();
			})
			->values()
			->all();
	}

	public function listBanksForMetas($userSectorId, $userClientIds)
	{
		$query = Banco::query()
			->select(array('banco_id', 'banco_name', 'banco_class'))
			->where('banco_status', 'Y');

		if ((int) $userSectorId !== 0) {
			$query->where('banco_area', (int) $userSectorId);
		}

		$clientIds = $this->parseIdList($userClientIds);
		if (!empty($clientIds)) {
			$query->whereIn('banco_id', $clientIds);
		}

		return $query->orderBy('banco_cod')
			->get()
			->map(function (Banco $banco) {
				return $banco->toArray();
			})
			->values()
			->all();
	}

	public function listAdminBanks($userSectorId, $userClientIds)
	{
		$query = Banco::query()
			->select(array('banco_id', 'banco_name'))
			->where('banco_status', 'Y');

		if ((int) $userSectorId !== 0) {
			$query->where('banco_area', (int) $userSectorId);
		}

		$clientIds = $this->parseIdList($userClientIds);
		if (!empty($clientIds)) {
			$query->whereIn('banco_id', $clientIds);
		}

		return $query->orderBy('banco_area')
			->orderBy('banco_name')
			->get()
			->map(function (Banco $banco) {
				return $banco->toArray();
			})
			->values()
			->all();
	}

	private function parseIdList($value)
	{
		$ids = array();
		foreach (explode(',', (string) $value) as $item) {
			$item = trim($item);
			if ($item !== '' && ctype_digit($item) && (int) $item > 0) {
				$ids[(int) $item] = (int) $item;
			}
		}

		return array_values($ids);
	}
}
