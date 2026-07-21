<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\RegionRepository;
use App\Repositories\UserRepository;
use App\Support\WriteResult;
use Illuminate\Support\Facades\Auth;

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
		$credentials = array(
			'login_usu' => (string) $login,
			'password' => (string) $password,
		);

		if (!Auth::guard('web')->attempt($credentials, false)) {
			return false;
		}

		$authUser = Auth::guard('web')->user();
		if ($authUser === null) {
			return false;
		}

		$storedHash = method_exists($authUser, 'getAuthPassword')
			? (string) $authUser->getAuthPassword()
			: '';

		$user = method_exists($authUser, 'toArray') ? $authUser->toArray() : array();

		if ($this->passwordNeedsRehash($storedHash)) {
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
		Auth::guard('web')->logout();

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
		$authUser = Auth::guard('web')->user();
		if ($authUser !== null) {
			return method_exists($authUser, 'toArray') ? $authUser->toArray() : false;
		}

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

		if (!Auth::guard('web')->check() || (int) Auth::guard('web')->id() !== (int) $user['id_usu']) {
			$authUser = \App\Models\Usuario::query()->find((int) $user['id_usu']);
			if ($authUser) {
				Auth::guard('web')->login($authUser, false);
			}
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
			return WriteResult::error();
		}

		return $this->users->updatePasswordAndAccess($id, $hash)
			? WriteResult::success()
			: WriteResult::error();
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
