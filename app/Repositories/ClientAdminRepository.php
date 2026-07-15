<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Area;
use App\Models\Banco;
use App\Models\Carteira;
use App\Models\Dado;
use Illuminate\Support\Facades\DB;

class ClientAdminRepository
{
	/** @var resource|null */
	private $sqlsrvConnection;

	/** @var int */
	private $lastInsertId = 0;

	public function __construct($sqlsrvConnection = null)
	{
		$this->sqlsrvConnection = $sqlsrvConnection;
	}

	public function all()
	{
		return DB::table('bancos as b')
			->join('area as a', 'a.area_id', '=', 'b.banco_area')
			->orderBy('b.banco_id')
			->get(array(
				'b.*',
				'a.area_nome',
				DB::raw("DATE_FORMAT(b.banco_creator, '%d/%m/%Y') AS datacad"),
			))
			->map(function ($row) {
				return (array) $row;
			})
			->all();
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
		if (!$this->sqlsrvConnection || !function_exists('sqlsrv_query')) {
			return array();
		}

		$sql = "SELECT c.CART_Descricao AS Carteira
			FROM Carteira AS c WITH (NOLOCK)
			WHERE c.CART_Descricao IS NOT NULL
			  AND LTRIM(RTRIM(c.CART_Descricao)) <> ''
			GROUP BY c.CART_Descricao
			ORDER BY c.CART_Descricao";
		$query = sqlsrv_query($this->sqlsrvConnection, $sql);
		if ($query === false) {
			return array();
		}

		$rows = array();
		while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
			$rows[] = $row;
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
