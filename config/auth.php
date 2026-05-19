<?php

declare(strict_types=1);

use App\Support\Env;

return array(
	'case_sensitive' => (bool) Env::get('AUTH_CASE_SENSITIVE', false),
	'validate_always' => (bool) Env::get('AUTH_VALIDATE_ALWAYS', true),
	'login_page' => Env::get('AUTH_LOGIN_PAGE', 'login.php'),
	'user_table' => Env::get('AUTH_USER_TABLE', 'usuarios'),
);
