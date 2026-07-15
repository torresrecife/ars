<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Andamento;

class AndamentoAdminRepository
{
	public function findById($andamentoId)
	{
		$row = Andamento::query()->find((int) $andamentoId);

		return $row ? $row->toArray() : false;
	}

	public function listAll()
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

	public function existsByKeyOrName($nome, $chave, $excludeId = 0)
	{
		$query = Andamento::query()
			->where(function ($query) use ($nome, $chave) {
				$query->where('nome', (string) $nome)
					->orWhere('chave', (string) $chave);
			});

		if ((int) $excludeId > 0) {
			$query->where('anda_id', '<>', (int) $excludeId);
		}

		return $query->exists();
	}

	public function insert(array $data)
	{
		$model = new Andamento();
		$model->fill($data);

		return $model->save();
	}

	public function update($andamentoId, array $data)
	{
		$model = Andamento::query()->find((int) $andamentoId);
		if (!$model) {
			return false;
		}

		$model->fill($data);

		return $model->save();
	}

	public function delete($andamentoId)
	{
		$model = Andamento::query()->find((int) $andamentoId);
		if (!$model) {
			return false;
		}

		return (bool) $model->delete();
	}
}
