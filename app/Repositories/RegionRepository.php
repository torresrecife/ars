<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Regiao;
use App\Models\RegiaoUf;
use App\Models\UsuarioRegiao;
use Illuminate\Support\Facades\DB;

class RegionRepository
{
	public function listActiveRegions()
	{
		return Regiao::query()
			->select(array('regiao_id', 'regiao_nome', 'regiao_slug', 'regiao_status'))
			->where('regiao_status', 'Y')
			->orderBy('regiao_nome')
			->get()
			->map(function (Regiao $regiao) {
				return $regiao->toArray();
			})
			->values()
			->all();
	}

	public function listRegionsByIds(array $ids)
	{
		$cleanIds = $this->cleanIds($ids);
		if (empty($cleanIds)) {
			return array();
		}

		return Regiao::query()
			->select(array('regiao_id', 'regiao_nome', 'regiao_slug', 'regiao_status'))
			->whereIn('regiao_id', $cleanIds)
			->orderBy('regiao_nome')
			->get()
			->map(function (Regiao $regiao) {
				return $regiao->toArray();
			})
			->values()
			->all();
	}

	public function listRegionIdsByUserId($userId)
	{
		return UsuarioRegiao::query()
			->where('usuario_id', (int) $userId)
			->orderBy('regiao_id')
			->pluck('regiao_id')
			->map(function ($id) {
				return (int) $id;
			})
			->unique()
			->values()
			->all();
	}

	public function listUfCodesByUserId($userId)
	{
		return DB::table('usuarios_regioes as ur')
			->join('regioes_ufs as ru', 'ru.regiao_id', '=', 'ur.regiao_id')
			->where('ur.usuario_id', (int) $userId)
			->orderBy('ru.uf')
			->distinct()
			->pluck('ru.uf')
			->map(function ($uf) {
				return strtoupper(trim((string) $uf));
			})
			->filter()
			->unique()
			->values()
			->all();
	}

	public function listUfCodesByRegionIds(array $regionIds)
	{
		$cleanIds = $this->cleanIds($regionIds);
		if (empty($cleanIds)) {
			return array();
		}

		return RegiaoUf::query()
			->whereIn('regiao_id', $cleanIds)
			->orderBy('uf')
			->distinct()
			->pluck('uf')
			->map(function ($uf) {
				return strtoupper(trim((string) $uf));
			})
			->filter()
			->unique()
			->values()
			->all();
	}

	public function replaceUserRegions($userId, array $regionIds)
	{
		$userId = (int) $userId;
		if ($userId <= 0) {
			return false;
		}

		$cleanIds = $this->cleanIds($regionIds);

		try {
			DB::transaction(function () use ($userId, $cleanIds) {
				UsuarioRegiao::query()->where('usuario_id', $userId)->delete();

				foreach ($cleanIds as $regionId) {
					UsuarioRegiao::query()->create(array(
						'usuario_id' => $userId,
						'regiao_id' => $regionId,
					));
				}
			});

			return true;
		} catch (\Exception $exception) {
			return false;
		}
	}

	private function cleanIds(array $ids)
	{
		$clean = array();
		foreach ($ids as $id) {
			$id = (int) $id;
			if ($id > 0) {
				$clean[$id] = $id;
			}
		}

		return array_values($clean);
	}
}
