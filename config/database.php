<?php

declare(strict_types=1);

use App\Support\Env;

return array(
	'default' => Env::get('DB_CONNECTION', 'mysql'),
	'connections' => array(
		'mysql' => array(
			'driver' => 'mysql',
			'host' => Env::get('DB_HOST', Env::get('DB_MYSQL_HOST', '127.0.0.1')),
			'port' => (int) Env::get('DB_PORT', Env::get('DB_MYSQL_PORT', 3306)),
			'database' => Env::get('DB_DATABASE', Env::get('DB_MYSQL_NAME', '')),
			'username' => Env::get('DB_USERNAME', Env::get('DB_MYSQL_USER', '')),
			'password' => Env::get('DB_PASSWORD', Env::get('DB_MYSQL_PASS', '')),
			'charset' => Env::get('DB_CHARSET', Env::get('DB_MYSQL_CHARSET', 'utf8mb4')),
		),
		'sqlsrv' => array(
			'driver' => 'sqlsrv',
			'host' => Env::get('SQLSRV_DB_HOST', Env::get('DB_SQLSRV_HOST', '127.0.0.1')),
			'port' => (int) Env::get('SQLSRV_DB_PORT', 1433),
			'database' => Env::get('SQLSRV_DB_DATABASE', Env::get('DB_SQLSRV_NAME', '')),
			'username' => Env::get('SQLSRV_DB_USERNAME', Env::get('DB_SQLSRV_USER', '')),
			'password' => Env::get('SQLSRV_DB_PASSWORD', Env::get('DB_SQLSRV_PASS', '')),
			'charset' => Env::get('SQLSRV_DB_CHARSET', Env::get('DB_SQLSRV_CHARSET', 'UTF-8')),
		),
	),
);
