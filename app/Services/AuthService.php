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
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}

		session_regenerate_id(true);
		$_SESSION['usuarioID'] = $user['id_usu'];
		$_SESSION['usuarioNome'] = $user['nome_usu'];
		$_SESSION['usuarioNivel'] = $user['nivel_usu'];
		$_SESSION['usuarioST'] = $user['status_usu'];
		$_SESSION['usuarioSetor'] = $user['id_setor'];
		$_SESSION['usuarioCliente'] = $user['id_cliente'];
		$_SESSION['usuarioRegiaoModo'] = isset($user['regiao_modo']) ? (string) $user['regiao_modo'] : 'N';
		$_SESSION['usuarioRegiaoIds'] = '';
		$_SESSION['usuarioRegiaoUfs'] = '';

		if ($this->regions !== null && isset($user['id_usu'])) {
			$regionIds = $this->regions->listRegionIdsByUserId((int) $user['id_usu']);
			$ufs = $this->regions->listUfCodesByUserId((int) $user['id_usu']);
			$_SESSION['usuarioRegiaoIds'] = implode(',', $regionIds);
			$_SESSION['usuarioRegiaoUfs'] = implode(',', $ufs);
		}
	}

	public function clearUserSession()
	{
		unset(
			$_SESSION['usuarioID'],
			$_SESSION['usuarioNome'],
			$_SESSION['usuarioNivel'],
			$_SESSION['usuarioST'],
			$_SESSION['usuarioSetor'],
			$_SESSION['usuarioCliente'],
			$_SESSION['usuarioRegiaoModo'],
			$_SESSION['usuarioRegiaoIds'],
			$_SESSION['usuarioRegiaoUfs'],
			$_SESSION['usuarioLogin'],
			$_SESSION['usuarioSenha']
		);
	}

	public function currentUser()
	{
		if (!isset($_SESSION['usuarioID'])) {
			return false;
		}

		return $this->users->findById($_SESSION['usuarioID']);
	}

	public function refreshUserAccess($id)
	{
		return $this->users->refreshAccess($id);
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
