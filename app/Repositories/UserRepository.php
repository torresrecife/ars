<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Usuario;
use mysqli;

class UserRepository
{
	/** @var mysqli|null */
	private $connection;

	/** @var string */
	private $table;

	public function __construct(mysqli $connection = null, $table = 'usuarios')
	{
		$this->connection = $connection;
		$this->table = (string) $table;
	}

	public function findByLogin($login)
	{
		if ($this->connection instanceof mysqli) {
			$sql = "SELECT * FROM `" . $this->table . "` WHERE `login_usu` = ? LIMIT 1";
			$stmt = mysqli_prepare($this->connection, $sql);
			if (!$stmt) {
				return false;
			}

			mysqli_stmt_bind_param($stmt, 's', $login);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$user = $result ? mysqli_fetch_assoc($result) : false;
			mysqli_stmt_close($stmt);

			return $user;
		}

		$user = Usuario::query()
			->where('login_usu', (string) $login)
			->first();

		return $user ? $user->toArray() : false;
	}

	public function findById($id)
	{
		if ($this->connection instanceof mysqli) {
			$id = (int) $id;
			$sql = "SELECT * FROM `" . $this->table . "` WHERE `id_usu` = ? LIMIT 1";
			$stmt = mysqli_prepare($this->connection, $sql);
			if (!$stmt) {
				return false;
			}

			mysqli_stmt_bind_param($stmt, 'i', $id);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$user = $result ? mysqli_fetch_assoc($result) : false;
			mysqli_stmt_close($stmt);

			return $user;
		}

		$user = Usuario::query()->find((int) $id);

		return $user ? $user->toArray() : false;
	}

	public function updatePassword($id, $hash)
	{
		if ($this->connection instanceof mysqli) {
			$id = (int) $id;
			$sql = "UPDATE `" . $this->table . "` SET `senha_usu` = ? WHERE `id_usu` = ? LIMIT 1";
			$stmt = mysqli_prepare($this->connection, $sql);
			if (!$stmt) {
				return false;
			}

			mysqli_stmt_bind_param($stmt, 'si', $hash, $id);
			$updated = mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);

			return $updated;
		}

		return (bool) Usuario::query()
			->whereKey((int) $id)
			->update(array('senha_usu' => (string) $hash));
	}

	public function updatePasswordAndAccess($id, $hash)
	{
		$accessTime = date('Y-m-d H:i:s');

		if ($this->connection instanceof mysqli) {
			$id = (int) $id;
			$sql = "UPDATE `" . $this->table . "` SET `senha_usu` = ?, `acesso_usu` = ? WHERE `id_usu` = ? LIMIT 1";
			$stmt = mysqli_prepare($this->connection, $sql);
			if (!$stmt) {
				return false;
			}

			mysqli_stmt_bind_param($stmt, 'ssi', $hash, $accessTime, $id);
			$updated = mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);

			return $updated;
		}

		return (bool) Usuario::query()
			->whereKey((int) $id)
			->update(array(
				'senha_usu' => (string) $hash,
				'acesso_usu' => $accessTime,
			));
	}

	public function refreshAccess($id)
	{
		$accessTime = date('Y-m-d H:i:s');

		if ($this->connection instanceof mysqli) {
			$id = (int) $id;
			$sql = "UPDATE `" . $this->table . "` SET `acesso_usu` = ? WHERE `id_usu` = ? LIMIT 1";
			$stmt = mysqli_prepare($this->connection, $sql);
			if (!$stmt) {
				return false;
			}

			mysqli_stmt_bind_param($stmt, 'si', $accessTime, $id);
			$updated = mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);

			return $updated;
		}

		return (bool) Usuario::query()
			->whereKey((int) $id)
			->update(array('acesso_usu' => $accessTime));
	}
}
