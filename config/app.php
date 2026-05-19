<?php

declare(strict_types=1);

use App\Support\Env;

return array(
	'name' => Env::get('APP_NAME', 'ARS'),
	'env' => Env::get('APP_ENV', 'local'),
	'debug' => (bool) Env::get('APP_DEBUG', false),
	'timezone' => Env::get('APP_TIMEZONE', 'America/Sao_Paulo'),
	'maintenance' => array(
		'neo_replica_wait_start_minute' => (int) Env::get('APP_NEO_WAIT_START_MINUTE', 0),
		'neo_replica_wait_end_minute' => (int) Env::get('APP_NEO_WAIT_END_MINUTE', 3),
	),
);
