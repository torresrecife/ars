<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserAdminRepository;

class UserAdminService
{
	/** @var UserAdminRepository */
	private $repository;

	public function __construct(UserAdminRepository $repository)
	{
		$this->repository = $repository;
	}

	public function indexData()
	{
		return array(
			'users' => $this->repository->all(),
			'areas' => $this->repository->listAreas(),
		);
	}

	public function editPayload($id)
	{
		$row = $this->repository->findById($id);
		if (!$row) {
			return '';
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

		return json_encode(array(
			'id_usu' => (int) $row['id_usu'],
			'nome_usu' => (string) $row['nome_usu'],
			'login_usu' => (string) $row['login_usu'],
			'email_usu' => (string) $row['email_usu'],
			'nivel_usu' => (string) $row['nivel_usu'],
			'id_setor' => (int) $row['id_setor'],
			'id_cliente' => isset($row['id_cliente']) ? (string) $row['id_cliente'] : '',
			'client_ids' => $clientIds,
			'clients' => $clients,
			'status_usu' => (string) $row['status_usu'],
		));
	}

	public function create(array $input)
	{
		$payload = $this->normalizePayload($input, false);
		if ($payload === false) {
			return '0';
		}

		if ($this->repository->findByLogin($payload['login_usu'])) {
			return '2';
		}

		return $this->repository->insert($payload) ? '1' : '0';
	}

	public function update(array $input)
	{
		$payload = $this->normalizePayload($input, true);
		if ($payload === false) {
			return '0';
		}

		if ($this->repository->findByLogin($payload['login_usu'], $payload['id_usu'])) {
			return '2';
		}

		return $this->repository->update($payload) ? '1' : '0';
	}

	public function delete($id)
	{
		return $this->repository->delete($id) ? '1' : '0';
	}

	private function normalizePayload(array $input, $isUpdate)
	{
		$nome = isset($input['nome_usu']) ? trim((string) $input['nome_usu']) : '';
		$login = isset($input['login_usu']) ? trim((string) $input['login_usu']) : '';
		if ($nome === '' || $login === '') {
			return false;
		}

		$senha = isset($input['senha_usu1']) ? (string) $input['senha_usu1'] : '';
		return array(
			'id_usu' => isset($input['id_usu']) ? (int) $input['id_usu'] : 0,
			'nome_usu' => $nome,
			'login_usu' => $login,
			'senha_usu' => $senha !== '' ? $this->hashPassword($senha) : '',
			'email_usu' => isset($input['email_usu']) ? trim((string) $input['email_usu']) : '',
			'nivel_usu' => isset($input['nivel_usu']) ? (string) $input['nivel_usu'] : '',
			'id_setor' => isset($input['setor_usu']) ? (int) $input['setor_usu'] : 0,
			'id_cliente' => isset($input['banco_neo']) ? trim((string) $input['banco_neo']) : '0',
			'status_usu' => isset($input['status_usu']) ? (string) $input['status_usu'] : '',
			'data_cad' => date('Y-m-d H:i:s'),
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
