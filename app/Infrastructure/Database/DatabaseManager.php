<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Support\Config;
use RuntimeException;
use mysqli;

class DatabaseManager
{
	/** @var Config */
	private $config;

	/** @var mysqli|null */
	private $mysql = null;

	/** @var resource|object|null */
	private $sqlsrv = null;

	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	public function mysql(): mysqli
	{
		if ($this->mysql instanceof mysqli) {
			return $this->mysql;
		}

		$host = (string) $this->config->get('database.connections.mysql.host', '');
		$user = (string) $this->config->get('database.connections.mysql.username', '');
		$pass = (string) $this->config->get('database.connections.mysql.password', '');
		$name = (string) $this->config->get('database.connections.mysql.database', '');
		$port = (int) $this->config->get('database.connections.mysql.port', 3306);

		$this->mysql = @new mysqli($host, $user, $pass, $name, $port);
		if ($this->mysql->connect_errno) {
			throw new RuntimeException('MySQL connection failed: ' . $this->mysql->connect_error);
		}

		if ($this->config->get('database.connections.mysql.charset')) {
			$this->mysql->set_charset((string) $this->config->get('database.connections.mysql.charset'));
		}

		return $this->mysql;
	}

	public function sqlsrv()
	{
		if ($this->sqlsrv !== null) {
			return $this->sqlsrv;
		}

		if (!function_exists('sqlsrv_connect')) {
			throw new RuntimeException('SQLSRV extension is not available.');
		}

		$host = (string) $this->config->get('database.connections.sqlsrv.host', '');
		$connectionInfo = array(
			'UID' => (string) $this->config->get('database.connections.sqlsrv.username', ''),
			'PWD' => (string) $this->config->get('database.connections.sqlsrv.password', ''),
			'Database' => (string) $this->config->get('database.connections.sqlsrv.database', ''),
			'CharacterSet' => (string) $this->config->get('database.connections.sqlsrv.charset', 'UTF-8'),
		);

		$this->sqlsrv = sqlsrv_connect($host, $connectionInfo);
		if ($this->sqlsrv === false) {
			throw new RuntimeException('SQLSRV connection failed.');
		}

		return $this->sqlsrv;
	}
}
