<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserAdminRepository;
use App\Support\WriteResult;

class UserAdminService
{
	/** @var UserAdminRepository */
	private $repository;

	/** @var RegionService */
	private $regionService;

	public function __construct(UserAdminRepository $repository, RegionService $regionService)
	{
		$this->repository = $repository;
		$this->regionService = $regionService;
	}

	public function indexData()
	{
		return array(
			'users' => $this->repository->all(),
			'areas' => $this->repository->listAreas(),
			'regions' => $this->regionService->listActive(),
		);
	}

	public function editPayload($id)
	{
		$row = $this->repository->findById($id);
		if (!$row) {
			return null;
		}

		$clientIds = array();
		foreach (explode(',', isset($row['id_cliente']) ? (string) $row['id_cliente'] : '') as $clientId) {
			$clientId = trim($clientId);
			if ($clientId !== '' && ctype_digit($clientId) && (int) $clientId > 0) {
				$clientIds[] = (int) $clientId;
			}
		}

		$clients = array();
		foreach ($this->repository->listClientsByIds($clientIds) as $client) {
			$clients[] = array(
				'id' => (int) $client['banco_id'],
				'name' => (string) $client['banco_name'],
			);
		}

		$regions = array();
		$regionIds = $this->regionService->listUserRegionIds((int) $row['id_usu']);
		foreach ($this->regionService->listUserRegions((int) $row['id_usu']) as $region) {
			$regions[] = array(
				'id' => (int) $region['regiao_id'],
				'name' => (string) $region['regiao_nome'],
			);
		}

		return array(
			'id_usu' => (int) $row['id_usu'],
			'nome_usu' => (string) $row['nome_usu'],
			'login_usu' => (string) $row['login_usu'],
			'email_usu' => (string) $row['email_usu'],
			'nivel_usu' => (string) $row['nivel_usu'],
			'id_setor' => (int) $row['id_setor'],
			'id_cliente' => isset($row['id_cliente']) ? (string) $row['id_cliente'] : '',
			'client_ids' => $clientIds,
			'clients' => $clients,
			'regiao_modo' => isset($row['regiao_modo']) ? (string) $row['regiao_modo'] : 'N',
			'region_ids' => $regionIds,
			'regions' => $regions,
			'status_usu' => (string) $row['status_usu'],
		);
	}

	public function create(array $input)
	{
		$payload = $this->normalizePayload($input, false);
		if ($payload === false) {
			return WriteResult::error();
		}

		if ($this->repository->findByLogin($payload['login_usu'])) {
			return WriteResult::duplicate();
		}

		if (!$this->repository->insert($payload)) {
			return WriteResult::error();
		}

		$userId = $this->repository->lastInsertId();
		if ($userId > 0 && !$this->regionService->syncUserRegions($userId, $payload['region_ids'])) {
			return WriteResult::error();
		}

		return WriteResult::success();
	}

	public function update(array $input)
	{
		$payload = $this->normalizePayload($input, true);
		if ($payload === false) {
			return WriteResult::error();
		}

		if ($this->repository->findByLogin($payload['login_usu'], $payload['id_usu'])) {
			return WriteResult::duplicate();
		}

		if (!$this->repository->update($payload)) {
			return WriteResult::error();
		}

		return $this->regionService->syncUserRegions($payload['id_usu'], $payload['region_ids'])
			? WriteResult::success()
			: WriteResult::error();
	}

	public function delete($id)
	{
		return $this->repository->delete($id) ? WriteResult::success() : WriteResult::error();
	}

	private function normalizePayload(array $input, $isUpdate)
	{
		$nome = isset($input['nome_usu']) ? trim((string) $input['nome_usu']) : '';
		$login = isset($input['login_usu']) ? trim((string) $input['login_usu']) : '';
		if ($nome === '' || $login === '') {
			return false;
		}

		$senha = isset($input['senha_usu1']) ? (string) $input['senha_usu1'] : '';
		$nivel = isset($input['nivel_usu']) ? (string) $input['nivel_usu'] : '';
		$regionIds = $this->parseIdList(isset($input['regiao_neo']) ? (string) $input['regiao_neo'] : '');
		$regionMode = isset($input['regiao_modo']) ? strtoupper(trim((string) $input['regiao_modo'])) : 'N';
		$regionConfig = $this->normalizeRegionConfig($nivel, $regionMode, $regionIds);

		return array(
			'id_usu' => isset($input['id_usu']) ? (int) $input['id_usu'] : 0,
			'nome_usu' => $nome,
			'login_usu' => $login,
			'senha_usu' => $senha !== '' ? $this->hashPassword($senha) : '',
			'email_usu' => isset($input['email_usu']) ? trim((string) $input['email_usu']) : '',
			'nivel_usu' => $nivel,
			'id_setor' => isset($input['setor_usu']) ? (int) $input['setor_usu'] : 0,
			'id_cliente' => isset($input['banco_neo']) ? trim((string) $input['banco_neo']) : '0',
			'regiao_modo' => $regionConfig['mode'],
			'region_ids' => $regionConfig['ids'],
			'status_usu' => isset($input['status_usu']) ? (string) $input['status_usu'] : '',
			'data_cad' => date('Y-m-d H:i:s'),
		);
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

	private function normalizeRegionConfig($level, $mode, array $regionIds)
	{
		$level = strtoupper(trim((string) $level));
		$mode = strtoupper(trim((string) $mode));
		if (!in_array($mode, array('N', 'R', 'T'), true)) {
			$mode = 'N';
		}

		if ($level === 'USU') {
			$regionIds = empty($regionIds) ? array() : array((int) reset($regionIds));
			return array(
				'mode' => empty($regionIds) ? 'N' : 'R',
				'ids' => $regionIds,
			);
		}

		if ($mode === 'R' && empty($regionIds)) {
			$mode = 'N';
		}

		if ($mode === 'T') {
			$regionIds = array_values($regionIds);
		}

		return array(
			'mode' => $mode,
			'ids' => array_values($regionIds),
		);
	}

	private function hashPassword($password)
	{
		if (function_exists('password_hash')) {
			return password_hash($password, PASSWORD_DEFAULT);
		}

		return md5((string) $password);
	}
}
