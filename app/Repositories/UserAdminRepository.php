<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

class UserAdminRepository
{
	/** @var mysqli */
	private $connection;

	public function __construct(mysqli $connection)
	{
		$this->connection = $connection;
	}

	public function all()
	{
		$result = mysqli_query($this->connection, "SELECT * FROM usuarios AS u ORDER BY u.id_usu");
		if (!$result) {
			return array();
		}

		$rows = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function findById($id)
	{
		$stmt = mysqli_prepare($this->connection, "SELECT * FROM usuarios WHERE id_usu = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		$id = (int) $id;
		mysqli_stmt_bind_param($stmt, 'i', $id);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row;
	}

	public function findByLogin($login, $excludeId = 0)
	{
		$sql = "SELECT * FROM usuarios WHERE login_usu = ?";
		$types = 's';
		$params = array((string) $login);

		if ((int) $excludeId > 0) {
			$sql .= " AND id_usu <> ?";
			$types .= 'i';
			$params[] = (int) $excludeId;
		}

		$sql .= " LIMIT 1";
		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		$this->bindParams($stmt, $types, $params);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = $result ? mysqli_fetch_assoc($result) : false;
		mysqli_stmt_close($stmt);

		return $row;
	}

	public function listAreas()
	{
		$result = mysqli_query($this->connection, "SELECT * FROM area ORDER BY area_id");
		if (!$result) {
			return array();
		}

		$rows = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function listClientsByIds(array $ids)
	{
		$cleanIds = array();
		foreach ($ids as $id) {
			$id = (int) $id;
			if ($id > 0) {
				$cleanIds[$id] = $id;
			}
		}

		if (empty($cleanIds)) {
			return array();
		}

		$sql = "SELECT banco_id, banco_name FROM bancos WHERE banco_id IN (" . implode(',', $cleanIds) . ") ORDER BY banco_name";
		$result = mysqli_query($this->connection, $sql);
		if (!$result) {
			return array();
		}

		$rows = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function insert(array $data)
	{
		$sql = "INSERT INTO usuarios (
			nome_usu, login_usu, senha_usu, email_usu, nivel_usu,
			id_setor, id_cliente, regiao_modo, acesso_usu, data_cad, status_usu
		) VALUES (?, ?, ?, ?, ?, ?, ?, ?, '0000-00-00 00:00:00', ?, ?)";

		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		$nome = (string) $data['nome_usu'];
		$login = (string) $data['login_usu'];
		$senha = (string) $data['senha_usu'];
		$email = (string) $data['email_usu'];
		$nivel = (string) $data['nivel_usu'];
		$setor = (int) $data['id_setor'];
		$cliente = (string) $data['id_cliente'];
		$regiaoModo = (string) $data['regiao_modo'];
		$dataCad = (string) $data['data_cad'];
		$status = (string) $data['status_usu'];

		mysqli_stmt_bind_param($stmt, 'sssssissss', $nome, $login, $senha, $email, $nivel, $setor, $cliente, $regiaoModo, $dataCad, $status);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	public function update(array $data)
	{
		$sql = "UPDATE usuarios SET
			nome_usu = ?,
			login_usu = ?,
			email_usu = ?,
			nivel_usu = ?,
			id_setor = ?,
			id_cliente = ?,
			regiao_modo = ?,
			status_usu = ?";
		$types = 'ssssisss';
		$params = array(
			(string) $data['nome_usu'],
			(string) $data['login_usu'],
			(string) $data['email_usu'],
			(string) $data['nivel_usu'],
			(int) $data['id_setor'],
			(string) $data['id_cliente'],
			(string) $data['regiao_modo'],
			(string) $data['status_usu'],
		);

		if ((string) $data['senha_usu'] !== '') {
			$sql .= ", senha_usu = ?";
			$types .= 's';
			$params[] = (string) $data['senha_usu'];
		}

		$sql .= " WHERE id_usu = ?";
		$types .= 'i';
		$params[] = (int) $data['id_usu'];

		$stmt = mysqli_prepare($this->connection, $sql);
		if (!$stmt) {
			return false;
		}

		$this->bindParams($stmt, $types, $params);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	public function lastInsertId()
	{
		return (int) mysqli_insert_id($this->connection);
	}

	public function delete($id)
	{
		$stmt = mysqli_prepare($this->connection, "DELETE FROM usuarios WHERE id_usu = ? LIMIT 1");
		if (!$stmt) {
			return false;
		}

		$id = (int) $id;
		mysqli_stmt_bind_param($stmt, 'i', $id);
		$ok = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		return $ok;
	}

	private function bindParams($stmt, $types, array $params)
	{
		$references = array($types);
		foreach ($params as $index => $value) {
			$references[] = &$params[$index];
		}

		call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt), $references));
	}
}
