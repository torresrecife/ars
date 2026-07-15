<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Semana;

class WeekRepository
{
	public function all()
	{
		return Semana::query()
			->orderBy('semanas_id')
			->get()
			->map(function (Semana $week) {
				$row = $week->toArray();
				$row['datacad'] = $week->data_cad ? $week->data_cad->format('d/m/Y H:i:s') : '';
				$row['dataalt'] = $week->data_cad ? $week->data_cad->format('d/m/Y H:i:s') : '';

				return $row;
			})
			->values()
			->all();
	}

	public function findById($weekId)
	{
		$row = Semana::query()->find((int) $weekId);

		return $row ? $row->toArray() : false;
	}

	public function existsByMonthYear($month, $year, $ignoreId = null)
	{
		$query = Semana::query()
			->where('mes', (int) $month)
			->where('ano', (int) $year);

		if ($ignoreId !== null) {
			$query->where('semanas_id', '<>', (int) $ignoreId);
		}

		return $query->exists();
	}

	public function insert(array $data)
	{
		$model = new Semana();
		$model->fill($data);

		return $model->save();
	}

	public function update($weekId, array $data)
	{
		$model = Semana::query()->find((int) $weekId);
		if (!$model) {
			return false;
		}

		$model->fill($data);

		return $model->save();
	}

	public function delete($weekId)
	{
		$model = Semana::query()->find((int) $weekId);
		if (!$model) {
			return false;
		}

		return (bool) $model->delete();
	}
}
