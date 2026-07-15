<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Andamento;
use App\Models\Banco;
use App\Models\MetaAndamento;
use Illuminate\Support\Facades\DB;

class MetaRepository
{
	public function findBankById($bankId)
	{
		$bank = Banco::query()
			->select(array('banco_id', 'banco_cod', 'banco_name', 'banco_class'))
			->where('banco_id', (int) $bankId)
			->first();

		return $bank ? $bank->toArray() : false;
	}

	public function listByBankMonthYear($bankId, $month, $year)
	{
		return DB::table('metas_andamentos as m')
			->join('andamentos as a', 'a.anda_id', '=', 'm.anda_id')
			->join('bancos as b', 'b.banco_id', '=', 'm.banco_id')
			->leftJoin('regioes as r', 'r.regiao_id', '=', 'm.regiao_id')
			->where('m.banco_id', (int) $bankId)
			->where('m.meta_mes', (int) $month)
			->where('m.meta_ano', (int) $year)
			->orderBy('a.especie')
			->orderBy('a.nome')
			->orderBy('r.regiao_nome')
			->orderBy('m.meta_id')
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
				'b.banco_name',
				'r.regiao_nome',
			))
			->map(function ($row) {
				return (array) $row;
			})
			->all();
	}

	public function findById($metaId)
	{
		$row = MetaAndamento::query()->find((int) $metaId);

		return $row ? $row->toArray() : false;
	}

	public function listAndamentos()
	{
		return Andamento::query()
			->orderBy('especie')
			->orderBy('nome')
			->get()
			->map(function (Andamento $andamento) {
				return $andamento->toArray();
			})
			->values()
			->all();
	}

	public function existsDuplicate(array $data, $excludeMetaId = 0)
	{
		$query = MetaAndamento::query()
			->where('banco_id', (int) $data['banco_id'])
			->where('meta_mes', (int) $data['meta_mes'])
			->where('meta_ano', (int) $data['meta_ano'])
			->where('anda_id', (int) $data['anda_id']);

		$regionId = isset($data['regiao_id']) && (int) $data['regiao_id'] > 0 ? (int) $data['regiao_id'] : 0;
		if ($regionId > 0) {
			$query->where('regiao_id', $regionId);
		} else {
			$query->whereNull('regiao_id');
		}

		if ((int) $excludeMetaId > 0) {
			$query->where('meta_id', '<>', (int) $excludeMetaId);
		}

		return $query->exists();
	}

	public function insert(array $data)
	{
		$model = new MetaAndamento();
		$model->fill($this->normalizeModelData($data));

		return $model->save();
	}

	public function update($metaId, array $data)
	{
		$model = MetaAndamento::query()->find((int) $metaId);
		if (!$model) {
			return false;
		}

		$model->fill($this->normalizeModelData($data));

		return $model->save();
	}

	public function delete($metaId)
	{
		$model = MetaAndamento::query()->find((int) $metaId);
		if (!$model) {
			return false;
		}

		return (bool) $model->delete();
	}

	private function normalizeModelData(array $data)
	{
		return array(
			'banco_id' => (int) $data['banco_id'],
			'meta_mes' => (int) $data['meta_mes'],
			'meta_ano' => (int) $data['meta_ano'],
			'anda_id' => (int) $data['anda_id'],
			'def_sem' => (string) $data['def_sem'],
			'sem_1' => $data['sem_1'],
			'sem_2' => $data['sem_2'],
			'sem_3' => $data['sem_3'],
			'sem_4' => $data['sem_4'],
			'sem_5' => $data['sem_5'],
			'meta_valor' => $data['meta_valor'],
			'regiao_id' => isset($data['regiao_id']) && (int) $data['regiao_id'] > 0 ? (int) $data['regiao_id'] : null,
		);
	}
}
