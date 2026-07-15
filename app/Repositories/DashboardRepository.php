<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Banco;
use App\Models\Carteira;
use App\Models\MetaAndamento;
use App\Models\Semana;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
	public function findBankById($bankId)
	{
		$row = Banco::query()
			->select(array('banco_id', 'banco_cod', 'banco_name', 'banco_class'))
			->where('banco_id', (int) $bankId)
			->first();

		return $row ? $row->toArray() : false;
	}

	public function findWeekByMonthYear($month, $year)
	{
		$row = Semana::query()
			->where('mes', (int) $month)
			->where('ano', (int) $year)
			->first();

		return $row ? $row->toArray() : false;
	}

	public function listMetaRowsByBankMonthYearAndSpecies($bankId, $month, $year, $species, $excludeNames = array(), $includeNames = array(), $regionId = 0, $regionIds = array())
	{
		$query = DB::table('metas_andamentos as m')
			->join('andamentos as a', 'a.anda_id', '=', 'm.anda_id')
			->where('m.banco_id', (int) $bankId)
			->where('m.meta_mes', (int) $month)
			->where('m.meta_ano', (int) $year)
			->where('a.especie', (int) $species);

		$regionId = (int) $regionId;
		$regionIds = array_values(array_unique(array_filter(array_map('intval', (array) $regionIds))));

		if ($regionId > 0) {
			$query->where('m.regiao_id', $regionId);
		} elseif (!empty($regionIds)) {
			$query->where(function ($query) use ($regionIds) {
				$query->whereNull('m.regiao_id')
					->orWhereIn('m.regiao_id', $regionIds);
			});
		} else {
			$query->whereNull('m.regiao_id');
		}

		foreach ($excludeNames as $excludeName) {
			$query->where('a.nome', '<>', (string) $excludeName);
		}

		if (!empty($includeNames)) {
			$query->where(function ($query) use ($includeNames) {
				foreach ($includeNames as $includeName) {
					$query->orWhere(function ($query) use ($includeName) {
						$query->where('a.nome', (string) $includeName)
							->orWhere('a.chave', (string) $includeName)
							->orWhere('a.anda_neo', (string) $includeName);
					});
				}
			});
		}

		return $query
			->orderBy('a.especie')
			->orderBy('a.ordem')
			->orderBy('a.nome')
			->get(array(
				'm.meta_id',
				'm.banco_id',
				'm.meta_mes',
				'm.meta_ano',
				'm.anda_id',
				'm.meta_valor',
				'm.def_sem',
				'm.sem_1',
				'm.sem_2',
				'm.sem_3',
				'm.sem_4',
				'm.sem_5',
				'm.regiao_id',
				'a.nome',
				'a.especie',
				'a.anda_neo',
				'a.ordem',
				'a.chave',
			))
			->map(function ($row) {
				return (array) $row;
			})
			->all();
	}

	public function findMetaRowByBankMonthYearAndAndaId($bankId, $month, $year, $andaId, $regionId = 0)
	{
		$query = DB::table('metas_andamentos as m')
			->join('andamentos as a', 'a.anda_id', '=', 'm.anda_id')
			->where('m.banco_id', (int) $bankId)
			->where('m.meta_mes', (int) $month)
			->where('m.meta_ano', (int) $year)
			->where('m.anda_id', (int) $andaId);

		if ((int) $regionId > 0) {
			$query->where('m.regiao_id', (int) $regionId);
		} else {
			$query->orderByRaw('CASE WHEN m.regiao_id IS NULL THEN 0 ELSE 1 END')
				->orderBy('m.meta_id');
		}

		$row = $query->first(array(
			'm.meta_id',
			'm.banco_id',
			'm.meta_mes',
			'm.meta_ano',
			'm.anda_id',
			'm.meta_valor',
			'm.def_sem',
			'm.sem_1',
			'm.sem_2',
			'm.sem_3',
			'm.sem_4',
			'm.sem_5',
			'm.regiao_id',
			'a.nome',
			'a.especie',
			'a.anda_neo',
			'a.ordem',
			'a.chave',
		));

		return $row ? (array) $row : false;
	}

	public function findCarteiraConditionByBankId($bankId)
	{
		$row = Carteira::query()
			->select(array('carteira_vinc'))
			->where('banco_id', (int) $bankId)
			->where('carteira_condicao', 'Carteira')
			->first();

		return $row ? (string) $row->carteira_vinc : '';
	}

	public function listRegionIdsWithMetaRowsByBankMonthYear($bankId, $month, $year)
	{
		return MetaAndamento::query()
			->where('banco_id', (int) $bankId)
			->where('meta_mes', (int) $month)
			->where('meta_ano', (int) $year)
			->whereNotNull('regiao_id')
			->orderBy('regiao_id')
			->pluck('regiao_id')
			->map(function ($id) {
				return (int) $id;
			})
			->unique()
			->values()
			->all();
	}

	public function listCarteiraCodesByBankId($bankId)
	{
		return DB::table('dados as d')
			->join('carteira as c', 'c.banco_id', '=', 'd.banco_id')
			->where('d.banco_id', (int) $bankId)
			->pluck('d.dados_cod')
			->map(function ($code) {
				return trim((string) $code);
			})
			->filter(function ($code) {
				return $code !== '';
			})
			->unique()
			->values()
			->all();
	}
}
