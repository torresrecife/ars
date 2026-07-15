<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Regiao;
use App\Models\RegiaoUf;
use App\Models\UsuarioRegiao;
use Illuminate\Support\Facades\DB;

class RegionAdminRepository
{
	/** @var int */
	private $lastInsertId = 0;

	public function all()
	{
		return Regiao::query()
			->leftJoin('regioes_ufs as ru', 'ru.regiao_id', '=', 'regioes.regiao_id')
			->leftJoin('usuarios_regioes as ur', 'ur.regiao_id', '=', 'regioes.regiao_id')
			->groupBy('regioes.regiao_id', 'regioes.regiao_nome', 'regioes.regiao_slug', 'regioes.regiao_status')
			->orderBy('regioes.regiao_nome')
			->get(array(
				'regioes.regiao_id',
				'regioes.regiao_nome',
				'regioes.regiao_slug',
				'regioes.regiao_status',
				DB::raw("COALESCE(GROUP_CONCAT(DISTINCT ru.uf ORDER BY ru.uf SEPARATOR ', '), '') AS ufs"),
				DB::raw('COUNT(DISTINCT ur.usuario_id) AS total_usuarios'),
			))
			->map(function (Regiao $row) {
				return array(
					'regiao_id' => (int) $row->regiao_id,
					'regiao_nome' => (string) $row->regiao_nome,
					'regiao_slug' => (string) $row->regiao_slug,
					'regiao_status' => (string) $row->regiao_status,
					'ufs' => (string) $row->ufs,
					'total_usuarios' => (string) $row->total_usuarios,
				);
			})
			->values()
			->all();
	}

	public function findById($id)
	{
		$row = Regiao::query()
			->select(array('regiao_id', 'regiao_nome', 'regiao_slug', 'regiao_status'))
			->where('regiao_id', (int) $id)
			->first();

		return $row ? $row->toArray() : false;
	}

	public function findBySlug($slug, $excludeId = 0)
	{
		$query = Regiao::query()
			->select(array('regiao_id'))
			->where('regiao_slug', (string) $slug);

		if ((int) $excludeId > 0) {
			$query->where('regiao_id', '<>', (int) $excludeId);
		}

		$row = $query->first();

		return $row ? $row->toArray() : false;
	}

	public function listUfsByRegionId($regionId)
	{
		$rows = RegiaoUf::query()
			->where('regiao_id', (int) $regionId)
			->orderBy('uf')
			->pluck('uf')
			->all();

		$ufs = array();
		foreach ($rows as $uf) {
			$uf = strtoupper(trim((string) $uf));
			if ($uf !== '') {
				$ufs[$uf] = $uf;
			}
		}

		return array_values($ufs);
	}

	public function insert(array $data)
	{
		$model = new Regiao();
		$model->regiao_nome = (string) $data['regiao_nome'];
		$model->regiao_slug = (string) $data['regiao_slug'];
		$model->regiao_status = (string) $data['regiao_status'];
		$model->data_cad = date('Y-m-d H:i:s');

		$ok = $model->save();
		$this->lastInsertId = $ok ? (int) $model->regiao_id : 0;

		return $ok;
	}

	public function update(array $data)
	{
		$model = Regiao::query()->find((int) $data['regiao_id']);
		if (!$model) {
			return false;
		}

		$model->regiao_nome = (string) $data['regiao_nome'];
		$model->regiao_slug = (string) $data['regiao_slug'];
		$model->regiao_status = (string) $data['regiao_status'];
		$model->data_alt = date('Y-m-d H:i:s');

		return $model->save();
	}

	public function delete($id)
	{
		if ($this->countUsersByRegionId($id) > 0) {
			return 'LINKED_USERS';
		}

		$model = Regiao::query()->find((int) $id);
		if (!$model) {
			return false;
		}

		return (bool) $model->delete();
	}

	public function countUsersByRegionId($regionId)
	{
		return (int) UsuarioRegiao::query()
			->where('regiao_id', (int) $regionId)
			->count();
	}

	public function replaceUfs($regionId, array $ufs)
	{
		$regionId = (int) $regionId;
		if ($regionId <= 0) {
			return false;
		}

		$ufs = $this->normalizeUfs($ufs);

		try {
			DB::transaction(function () use ($regionId, $ufs) {
				RegiaoUf::query()->where('regiao_id', $regionId)->delete();

				foreach ($ufs as $uf) {
					RegiaoUf::query()->create(array(
						'regiao_id' => $regionId,
						'uf' => $uf,
					));
				}
			});

			return true;
		} catch (\Exception $exception) {
			return false;
		}
	}

	public function lastInsertId()
	{
		return $this->lastInsertId;
	}

	private function normalizeUfs(array $ufs)
	{
		$clean = array();
		foreach ($ufs as $uf) {
			$uf = strtoupper(trim((string) $uf));
			if (preg_match('/^[A-Z]{2}$/', $uf)) {
				$clean[$uf] = $uf;
			}
		}

		return array_values($clean);
	}
}
