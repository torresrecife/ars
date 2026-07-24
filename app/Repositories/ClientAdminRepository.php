<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Area;
use App\Models\Banco;
use App\Models\Carteira;
use App\Models\Dado;
use App\Repositories\SqlsrvLookupRepository;
use Illuminate\Support\Facades\DB;

class ClientAdminRepository
{
	/** @var SqlsrvLookupRepository */
	private $sqlsrvLookupRepository;

	/** @var int */
	private $lastInsertId = 0;

	public function __construct(SqlsrvLookupRepository $sqlsrvLookupRepository)
	{
		$this->sqlsrvLookupRepository = $sqlsrvLookupRepository;
	}

	public function paginate($perPage = 20, $search = '')
	{
		$query = DB::table('bancos as b')
			->join('area as a', 'a.area_id', '=', 'b.banco_area');

		$search = trim((string) $search);
		if ($search !== '') {
			$query->where(function ($query) use ($search) {
				$query->where('b.banco_name', 'like', '%' . $search . '%')
					->orWhere('b.banco_cod', 'like', '%' . $search . '%')
					->orWhere('b.banco_curto', 'like', '%' . $search . '%')
					->orWhere('a.area_nome', 'like', '%' . $search . '%');
			});
		}

		$paginator = $query
			->orderBy('b.banco_id')
			->paginate((int) $perPage, array(
				'b.*',
				'a.area_nome',
				DB::raw("DATE_FORMAT(b.banco_creator, '%d/%m/%Y') AS datacad"),
			));

		$paginator->setCollection($paginator->getCollection()->map(function ($row) {
				return (array) $row;
			})->values());

		return $paginator;
	}

	public function listDadosByBanco()
	{
		return Dado::query()
			->select(array('banco_id', 'dados_cod'))
			->orderBy('banco_id')
			->orderBy('dados_id')
			->get()
			->map(function (Dado $dado) {
				return $dado->toArray();
			})
			->values()
			->all();
	}

	public function listDadosByBancoId($bancoId)
	{
		return Dado::query()
			->select(array('dados_cod'))
			->where('banco_id', (int) $bancoId)
			->orderBy('dados_id')
			->get()
			->map(function (Dado $dado) {
				return $dado->toArray();
			})
			->values()
			->all();
	}

	public function findById($id)
	{
		$row = Banco::query()->find((int) $id);

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

	public function listCarteiras()
	{
		$rows = array();
		foreach ($this->sqlsrvLookupRepository->listCarteiras() as $carteira) {
			$rows[] = array('Carteira' => $carteira);
		}

		return $rows;
	}

	public function insertBank(array $data)
	{
		$model = new Banco();
		$model->fill(array(
			'banco_name' => (string) $data['banco_name'],
			'banco_cod' => (string) $data['banco_cod'],
			'banco_creator' => (string) $data['banco_creator'],
			'banco_area' => (int) $data['banco_area'],
			'banco_status' => (string) $data['banco_status'],
			'banco_class' => (string) $data['banco_class'],
			'simulador' => (int) $data['simulador'],
			'banco_curto' => (string) $data['banco_curto'],
		));

		$ok = $model->save();
		$this->lastInsertId = $ok ? (int) $model->banco_id : 0;

		return $ok;
	}

	public function updateBank(array $data)
	{
		$model = Banco::query()->find((int) $data['banco_id']);
		if (!$model) {
			return false;
		}

		$model->fill(array(
			'banco_name' => (string) $data['banco_name'],
			'banco_cod' => (string) $data['banco_cod'],
			'banco_creator' => (string) $data['banco_creator'],
			'banco_area' => (int) $data['banco_area'],
			'banco_status' => (string) $data['banco_status'],
			'banco_class' => (string) $data['banco_class'],
			'simulador' => (int) $data['simulador'],
			'banco_curto' => (string) $data['banco_curto'],
		));

		return $model->save();
	}

	public function getLastInsertId()
	{
		return $this->lastInsertId;
	}

	public function createCarteira($bancoId, $date)
	{
		$model = new Carteira();
		$model->fill(array(
			'banco_id' => (int) $bancoId,
			'carteira_condicao' => 'Carteira',
			'carteira_cod' => 1,
			'carteira_vinc' => 'IN',
			'carteira_date' => (string) $date,
			'carteira_status' => 'Y',
		));

		return $model->save();
	}

	public function findCarteiraIdByBanco($bancoId)
	{
		$row = Carteira::query()
			->where('banco_id', (int) $bancoId)
			->orderByDesc('carteira_id')
			->first();

		return $row ? (int) $row->carteira_id : 0;
	}

	public function deleteDadosByBanco($bancoId)
	{
		Dado::query()->where('banco_id', (int) $bancoId)->delete();

		return true;
	}

	public function insertDados($carteiraId, $bancoId, $dadosCod, $date)
	{
		$model = new Dado();
		$model->fill(array(
			'carteira_id' => (int) $carteiraId,
			'banco_id' => (int) $bancoId,
			'dados_cod' => (string) $dadosCod,
			'dados_date' => (string) $date,
			'dados_status' => 'Y',
		));

		return $model->save();
	}

	public function deleteBankCascade($bancoId)
	{
		$bancoId = (int) $bancoId;

		try {
			DB::transaction(function () use ($bancoId) {
				Dado::query()->where('banco_id', $bancoId)->delete();
				Carteira::query()->where('banco_id', $bancoId)->delete();
				Banco::query()->where('banco_id', $bancoId)->delete();
			});

			return true;
		} catch (\Exception $exception) {
			return false;
		}
	}
}
