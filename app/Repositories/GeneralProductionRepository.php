<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Area;
use App\Models\Banco;
use App\Models\Carteira;
use App\Models\MetaAndamento;
use App\Models\Semana;
use Illuminate\Support\Facades\DB;

class GeneralProductionRepository
{
	public function findWeekByMonthYear($month, $year)
	{
		$row = Semana::query()
			->where('mes', (int) $month)
			->where('ano', (int) $year)
			->first();

		return $row ? $row->toArray() : false;
	}

	public function findAreaNameById($areaId)
	{
		$row = Area::query()
			->select(array('area_nome'))
			->where('area_id', (int) $areaId)
			->first();

		return $row ? (string) $row->area_nome : '';
	}

	public function listBanks($userSectorId, $startSector, $userClientIds, $activeOnly)
	{
		$query = Banco::query()
			->select(array('banco_id', 'banco_name', 'banco_class', 'banco_status'));

		if ($activeOnly) {
			$query->where('banco_status', 'Y');
		}

		if ((int) $userSectorId !== 0) {
			$query->where('banco_area', (int) $userSectorId);
		} elseif ((string) $startSector !== '') {
			$query->where('banco_area', (int) $startSector);
		}

		$clientIds = $this->parseIdList($userClientIds);
		if (!empty($clientIds)) {
			$query->whereIn('banco_id', $clientIds);
		}

		return $query->orderBy('banco_name')
			->get()
			->map(function (Banco $banco) {
				return $banco->toArray();
			})
			->values()
			->all();
	}

	public function listFinancialMetasByBankMonthYear($bankId, $month, $year, $regionId = 0)
	{
		$query = DB::table('metas_andamentos as m')
			->join('andamentos as a', 'a.anda_id', '=', 'm.anda_id')
			->where('m.banco_id', (int) $bankId)
			->where('m.meta_mes', (int) $month)
			->where('m.meta_ano', (int) $year)
			->where('a.especie', 2);

		if ((int) $regionId > 0) {
			$query->where('m.regiao_id', (int) $regionId);
		} else {
			$query->whereNull('m.regiao_id');
		}

		return $query->orderBy('m.meta_id')
			->get(array(
				'm.meta_id',
				'm.meta_valor',
				'm.def_sem',
				'm.sem_1',
				'm.sem_2',
				'm.sem_3',
				'm.sem_4',
				'm.sem_5',
				'm.regiao_id',
				'a.anda_neo',
			))
			->map(function ($row) {
				return (array) $row;
			})
			->all();
	}

	public function findCarteiraModeByBankId($bankId)
	{
		$row = Carteira::query()
			->select(array('carteira_vinc'))
			->where('banco_id', (int) $bankId)
			->where('carteira_condicao', 'Carteira')
			->first();

		return $row ? (string) $row->carteira_vinc : '';
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
