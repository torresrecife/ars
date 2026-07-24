<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AndamentoAdminRepository;
use App\Support\WriteResult;

class AndamentoAdminService
{
	/** @var AndamentoAdminRepository */
	private $repository;

	public function __construct(AndamentoAdminRepository $repository)
	{
		$this->repository = $repository;
	}

	public function editPayload($id)
	{
		$row = $this->repository->findById($id);
		if (!$row) {
			return null;
		}

		$tipos = array();
		foreach (explode(',', isset($row['anda_neo']) ? (string) $row['anda_neo'] : '') as $tipo) {
			$tipo = trim($tipo);
			if ($tipo !== '') {
				$tipos[] = $tipo;
			}
		}

		return array(
			'anda_id' => (int) $row['anda_id'],
			'nome' => (string) $row['nome'],
			'chave' => (string) $row['chave'],
			'anda_neo' => (string) $row['anda_neo'],
			'especie' => (string) $row['especie'],
			'painel' => (string) $row['painel'],
			'titulo' => (string) $row['titulo'],
			'tipos' => $tipos,
		);
	}

	public function indexData()
	{
		$search = trim((string) request()->query('q', ''));

		return array(
			'andamentos' => $this->repository->paginate(20, $search),
			'search' => $search,
			'metaTipos' => array(
				1 => __('Production'),
				2 => __('Financial'),
			),
		);
	}

	public function formData(array $values = array())
	{
		$defaults = array(
			'anda_id' => 0,
			'nome' => '',
			'chave' => '',
			'anda_neo' => '',
			'especie' => '',
			'painel' => 'N',
			'titulo' => '',
			'tipos' => array(),
		);

		$data = array_merge($defaults, $values);
		if (empty($data['tipos']) && !empty($data['anda_neo'])) {
			$data['tipos'] = $this->splitTipos((string) $data['anda_neo']);
		}

		return array(
			'andamento' => $data,
			'metaTipos' => array(
				1 => __('Production'),
				2 => __('Financial'),
			),
			'yesNoOptions' => array(
				'Y' => __('Yes'),
				'N' => __('No'),
			),
		);
	}

	public function create(array $input)
	{
		$payload = $this->normalizePayload($input);
		if ($payload === false) {
			return WriteResult::error();
		}

		if ($this->repository->existsByKeyOrName($payload['nome'], $payload['chave'])) {
			return WriteResult::duplicate();
		}

		return $this->repository->insert($payload) ? WriteResult::success() : WriteResult::error();
	}

	public function update(array $input)
	{
		$payload = $this->normalizePayload($input);
		$andamentoId = isset($input['anda_id']) ? (int) $input['anda_id'] : 0;
		if ($payload === false || $andamentoId <= 0) {
			return WriteResult::error();
		}

		if ($this->repository->existsByKeyOrName($payload['nome'], $payload['chave'], $andamentoId)) {
			return WriteResult::duplicate();
		}

		return $this->repository->update($andamentoId, $payload) ? WriteResult::success() : WriteResult::error();
	}

	public function delete($id)
	{
		return $this->repository->delete($id) ? WriteResult::success() : WriteResult::error();
	}

	private function normalizePayload(array $input)
	{
		$nome = isset($input['nome']) ? trim((string) $input['nome']) : '';
		$chave = isset($input['chave']) ? trim((string) $input['chave']) : '';
		if ($nome === '' || $chave === '') {
			return false;
		}

		return array(
			'nome' => $nome,
			'chave' => $chave,
			'anda_neo' => isset($input['anda_neo']) ? trim((string) $input['anda_neo']) : '',
			'especie' => isset($input['especie']) ? (int) $input['especie'] : 0,
			'painel' => isset($input['painel']) ? trim((string) $input['painel']) : '',
			'titulo' => isset($input['titulo']) ? trim((string) $input['titulo']) : '',
		);
	}

	private function splitTipos($value)
	{
		$tipos = array();
		foreach (explode(',', (string) $value) as $tipo) {
			$tipo = trim($tipo);
			if ($tipo !== '') {
				$tipos[] = $tipo;
			}
		}

		return $tipos;
	}
}
