<?php

declare(strict_types=1);

namespace App\Support;

class LegacyConfig
{
	public static function build(array $config): array
	{
		$mysql = isset($config['database']['connections']['mysql']) ? (array) $config['database']['connections']['mysql'] : array();
		$sqlsrv = isset($config['database']['connections']['sqlsrv']) ? (array) $config['database']['connections']['sqlsrv'] : array();

		return array(
			'app' => array(
				'timezone' => (string) ($config['app']['timezone'] ?? 'America/Sao_Paulo'),
				'display_errors' => (bool) ($config['app']['display_errors'] ?? false),
			),
			'auth' => array(
				'case_sensitive' => (bool) ($config['auth']['case_sensitive'] ?? false),
				'validate_always' => (bool) ($config['auth']['validate_always'] ?? true),
				'login_page' => (string) ($config['auth']['login_page'] ?? 'login.php'),
				'user_table' => (string) ($config['auth']['user_table'] ?? 'usuarios'),
			),
			'db' => array(
				'mysql' => array(
					'host' => (string) ($mysql['host'] ?? ''),
					'port' => (int) ($mysql['port'] ?? 3306),
					'user' => (string) ($mysql['username'] ?? ''),
					'pass' => (string) ($mysql['password'] ?? ''),
					'name' => (string) ($mysql['database'] ?? ''),
					'charset' => (string) ($mysql['charset'] ?? 'utf8mb4'),
				),
				'sqlsrv' => array(
					'host' => (string) ($sqlsrv['host'] ?? ''),
					'port' => (int) ($sqlsrv['port'] ?? 1433),
					'user' => (string) ($sqlsrv['username'] ?? ''),
					'pass' => (string) ($sqlsrv['password'] ?? ''),
					'name' => (string) ($sqlsrv['database'] ?? ''),
					'charset' => (string) ($sqlsrv['charset'] ?? 'UTF-8'),
				),
			),
			'maintenance' => array(
				'neo_replica_wait_start_minute' => (int) ($config['app']['maintenance']['neo_replica_wait_start_minute'] ?? 0),
				'neo_replica_wait_end_minute' => (int) ($config['app']['maintenance']['neo_replica_wait_end_minute'] ?? 0),
			),
		);
	}
}
