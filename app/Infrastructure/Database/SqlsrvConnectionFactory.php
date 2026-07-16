<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use RuntimeException;

class SqlsrvConnectionFactory
{
	/**
	 * @var array<string,mixed>
	 */
	private $config;

	/** @var resource|object|null */
	private $connection;

	/**
	 * @param array<string,mixed> $config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
		$this->connection = null;
	}

	/**
	 * @return resource|object|null
	 */
	public function make()
	{
		if ($this->connection !== null) {
			return $this->connection;
		}

		if (!function_exists('sqlsrv_connect')) {
			return null;
		}

		$host = (string) ($this->config['host'] ?? '');
		$port = (string) ($this->config['port'] ?? '');
		$serverName = $host;
		if ($port !== '') {
			$serverName .= ',' . $port;
		}

		$connectionInfo = array(
			'UID' => (string) ($this->config['username'] ?? ''),
			'PWD' => (string) ($this->config['password'] ?? ''),
			'Database' => (string) ($this->config['database'] ?? ''),
			'CharacterSet' => (string) ($this->config['charset'] ?? 'UTF-8'),
		);

		$this->connection = sqlsrv_connect($serverName, $connectionInfo);
		if ($this->connection === false) {
			$this->connection = null;
			throw new RuntimeException('SQLSRV connection failed.');
		}

		return $this->connection;
	}
}
