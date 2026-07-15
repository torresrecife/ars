<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\RegionRepository;
use App\Repositories\UserRepository;

class AuthService
{
	/** @var UserRepository */
	private $users;

	/** @var RegionRepository|null */
	private $regions;

	public function __construct(UserRepository $users, RegionRepository $regions = null)
	{
		$this->users = $users;
		$this->regions = $regions;
	}

	public function hashPassword($password)
	{
		if (function_exists('password_hash')) {
			return password_hash($password, PASSWORD_DEFAULT);
		}

		return md5($password);
	}

	public function verifyPassword($password, $storedHash)
	{
		if (!is_string($storedHash) || $storedHash === '') {
			return false;
		}

		if (preg_match('/^[a-f0-9]{32}$/i', $storedHash)) {
			return hash_equals(strtolower($storedHash), strtolower(md5($password)));
		}

		if (function_exists('password_verify')) {
			return password_verify($password, $storedHash);
		}

		return false;
	}

	public function passwordNeedsRehash($storedHash)
	{
		if (!is_string($storedHash) || $storedHash === '') {
			return false;
		}

		if (preg_match('/^[a-f0-9]{32}$/i', $storedHash)) {
			return true;
		}

		if (function_exists('password_needs_rehash')) {
			return password_needs_rehash($storedHash, PASSWORD_DEFAULT);
		}

		return false;
	}

	public function attempt($login, $password)
	{
		$user = $this->users->findByLogin($login);
		if (empty($user)) {
			return false;
		}

		if (!$this->verifyPassword($password, $user['senha_usu'])) {
			return false;
		}

		if ($this->passwordNeedsRehash($user['senha_usu'])) {
			$newHash = $this->hashPassword($password);
			if ($newHash) {
				$this->users->updatePassword($user['id_usu'], $newHash);
			}
		}

		$this->storeUserSession($user);

		return $user;
	}

	public function storeUserSession(array $user)
	{
		$this->syncSessionContext($user, true);
	}

	public function clearUserSession()
	{
		if (!$this->hasLaravelSessionStore()) {
			return;
		}

		session()->forget(array(
			'usuarioID',
			'usuarioNome',
			'usuarioNivel',
			'usuarioST',
			'usuarioSetor',
			'usuarioCliente',
			'usuarioRegiaoModo',
			'usuarioRegiaoIds',
			'usuarioRegiaoUfs',
			'usuarioLogin',
			'usuarioSenha',
		));
	}

	public function currentUser()
	{
		if (!$this->hasLaravelSessionStore() || !session()->has('usuarioID')) {
			return false;
		}

		return $this->users->findById(session('usuarioID'));
	}

	public function syncSessionContext(array $user, $regenerateId = false)
	{
		$sessionData = array(
			'usuarioID' => $user['id_usu'],
			'usuarioNome' => $user['nome_usu'],
			'usuarioNivel' => $user['nivel_usu'],
			'usuarioST' => $user['status_usu'],
			'usuarioSetor' => $user['id_setor'],
			'usuarioCliente' => $user['id_cliente'],
			'usuarioRegiaoModo' => isset($user['regiao_modo']) ? (string) $user['regiao_modo'] : 'N',
			'usuarioRegiaoIds' => '',
			'usuarioRegiaoUfs' => '',
		);

		if ($this->regions !== null && isset($user['id_usu'])) {
			$regionIds = $this->regions->listRegionIdsByUserId((int) $user['id_usu']);
			$ufs = $this->regions->listUfCodesByUserId((int) $user['id_usu']);
			$sessionData['usuarioRegiaoIds'] = implode(',', $regionIds);
			$sessionData['usuarioRegiaoUfs'] = implode(',', $ufs);
		}

		if ($this->hasLaravelSessionStore()) {
			if ($regenerateId) {
				session()->migrate(true);
			}
			session($sessionData);
		}

		return $user;
	}

	private function hasLaravelSessionStore()
	{
		return function_exists('app')
			&& app()->bound('session')
			&& app()->bound('request');
	}

	public function refreshUserAccess($id)
	{
		return $this->users->refreshAccess($id);
	}

	public function requiresPasswordChange(array $user)
	{
		if (!array_key_exists('acesso_usu', $user)) {
			return true;
		}

		$lastAccess = $user['acesso_usu'];

		return $lastAccess === null
			|| $lastAccess === ''
			|| $lastAccess === '0000-00-00 00:00:00';
	}

	public function updatePasswordAndAccess($id, $password)
	{
		$hash = $this->hashPassword($password);
		if (!$hash) {
			return false;
		}

		return $this->users->updatePasswordAndAccess($id, $hash);
	}

	public function findUserByLogin($login)
	{
		return $this->users->findByLogin($login);
	}

	public function findUserById($id)
	{
		return $this->users->findById($id);
	}
}
