<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

class UserRepository
{
	/** @var mysqli */
	private $connection;

	/** @var string */
	private $table;

	public function __construct(mysqli $connection, $table)
	{
		$this->connection = $connection;
		$this->table = (string) $table;
	}

	public function findByLogin($login)
	{
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

	public function findById($id)
	{
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

	public function updatePassword($id, $hash)
	{
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

	public function updatePasswordAndAccess($id, $hash)
	{
		$id = (int) $id;
		$sql = "UPDATE `" . $this->table . "` SET `senha_usu` = ?, `acesso_usu` = ? WHERE `id_usu` = ? LIMIT 1";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		$accessTime = date('Y-m-d H:i:s');
		mysqli_stmt_bind_param($stmt, 'ssi', $hash, $accessTime, $id);
		$updated = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $updated;
	}

	public function refreshAccess($id)
	{
		$id = (int) $id;
		$sql = "UPDATE `" . $this->table . "` SET `acesso_usu` = ? WHERE `id_usu` = ? LIMIT 1";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		$accessTime = date('Y-m-d H:i:s');
		mysqli_stmt_bind_param($stmt, 'si', $accessTime, $id);
		$updated = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $updated;
	}
}
