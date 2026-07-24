<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Area;
use App\Models\Banco;
use App\Models\Usuario;

class UserAdminRepository
{
	/** @var int */
	private $lastInsertId = 0;

	public function paginate($perPage = 20, $search = '')
	{
		$query = Usuario::query();

		$search = trim((string) $search);
		if ($search !== '') {
			$query->where(function ($query) use ($search) {
				$query->where('nome_usu', 'like', '%' . $search . '%')
					->orWhere('login_usu', 'like', '%' . $search . '%')
					->orWhere('email_usu', 'like', '%' . $search . '%');
			});
		}

		$paginator = $query
			->orderBy('id_usu')
			->paginate((int) $perPage);

		$paginator->setCollection($paginator->getCollection()->map(function (Usuario $user) {
				return $user->toArray();
			})->values());

		return $paginator;
	}

	public function findById($id)
	{
		$row = Usuario::query()->find((int) $id);

		return $row ? $row->toArray() : false;
	}

	public function findByLogin($login, $excludeId = 0)
	{
		$query = Usuario::query()->where('login_usu', (string) $login);

		if ((int) $excludeId > 0) {
			$query->where('id_usu', '<>', (int) $excludeId);
		}

		$row = $query->first();

		return $row ? $row->toArray() : false;
	}

	public function listAreas()
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

	public function listClientsByIds(array $ids)
	{
		$cleanIds = array();
		foreach ($ids as $id) {
			$id = (int) $id;
			if ($id > 0) {
				$cleanIds[$id] = $id;
			}
		}

		if (empty($cleanIds)) {
			return array();
		}

		return Banco::query()
			->select(array('banco_id', 'banco_name'))
			->whereIn('banco_id', array_values($cleanIds))
			->orderBy('banco_name')
			->get()
			->map(function (Banco $banco) {
				return $banco->toArray();
			})
			->values()
			->all();
	}

	public function insert(array $data)
	{
		$model = new Usuario();
		$model->nome_usu = (string) $data['nome_usu'];
		$model->login_usu = (string) $data['login_usu'];
		$model->senha_usu = (string) $data['senha_usu'];
		$model->email_usu = (string) $data['email_usu'];
		$model->nivel_usu = (string) $data['nivel_usu'];
		$model->id_setor = (int) $data['id_setor'];
		$model->id_cliente = (string) $data['id_cliente'];
		$model->regiao_modo = (string) $data['regiao_modo'];
		$model->acesso_usu = null;
		$model->data_cad = (string) $data['data_cad'];
		$model->status_usu = (string) $data['status_usu'];

		$ok = $model->save();
		$this->lastInsertId = $ok ? (int) $model->id_usu : 0;

		return $ok;
	}

	public function update(array $data)
	{
		$model = Usuario::query()->find((int) $data['id_usu']);
		if (!$model) {
			return false;
		}

		$model->nome_usu = (string) $data['nome_usu'];
		$model->login_usu = (string) $data['login_usu'];
		$model->email_usu = (string) $data['email_usu'];
		$model->nivel_usu = (string) $data['nivel_usu'];
		$model->id_setor = (int) $data['id_setor'];
		$model->id_cliente = (string) $data['id_cliente'];
		$model->regiao_modo = (string) $data['regiao_modo'];
		$model->status_usu = (string) $data['status_usu'];

		if ((string) $data['senha_usu'] !== '') {
			$model->senha_usu = (string) $data['senha_usu'];
		}

		return $model->save();
	}

	public function lastInsertId()
	{
		return $this->lastInsertId;
	}

	public function delete($id)
	{
		$model = Usuario::query()->find((int) $id);
		if (!$model) {
			return false;
		}

		return (bool) $model->delete();
	}
}
