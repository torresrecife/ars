<?php

declare(strict_types=1);

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class LegacyUsuarioProvider extends EloquentUserProvider
{
	public function __construct(HasherContract $hasher, $model)
	{
		parent::__construct($hasher, $model);
	}

	public function validateCredentials(UserContract $user, array $credentials)
	{
		$plain = isset($credentials['password']) ? (string) $credentials['password'] : '';
		$storedHash = (string) $user->getAuthPassword();

		if ($plain === '' || $storedHash === '') {
			return false;
		}

		if (preg_match('/^[a-f0-9]{32}$/i', $storedHash)) {
			return hash_equals(strtolower($storedHash), strtolower(md5($plain)));
		}

		return $this->hasher->check($plain, $storedHash);
	}
}
