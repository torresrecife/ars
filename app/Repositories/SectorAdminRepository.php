<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Area;

class SectorAdminRepository
{
	public function listAll()
	{
		return Area::query()
			->orderBy('area_id')
			->get()
			->map(function (Area $area) {
				return $area->toArray();
			})
			->values()
			->all();
	}

	public function findById($areaId)
	{
		$row = Area::query()->find((int) $areaId);

		return $row ? $row->toArray() : false;
	}

	public function existsByName($name, $excludeId = 0)
	{
		$query = Area::query()->where('area_nome', (string) $name);

		if ((int) $excludeId > 0) {
			$query->where('area_id', '<>', (int) $excludeId);
		}

		return $query->exists();
	}

	public function insert($name)
	{
		$model = new Area();
		$model->area_nome = (string) $name;
		$model->area_date = date('Y-m-d H:i:s');

		return $model->save();
	}

	public function update($areaId, $name)
	{
		$model = Area::query()->find((int) $areaId);
		if (!$model) {
			return false;
		}

		$model->area_nome = (string) $name;

		return $model->save();
	}

	public function delete($areaId)
	{
		$model = Area::query()->find((int) $areaId);
		if (!$model) {
			return false;
		}

		return (bool) $model->delete();
	}
}
