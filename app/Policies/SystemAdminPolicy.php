<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Usuario;

class SystemAdminPolicy
{
	public function viewAny(Usuario $user, $resource = null)
	{
		return (string) $user->nivel_usu === 'ADM';
	}

	public function view(Usuario $user, $resource = null)
	{
		return (string) $user->nivel_usu === 'ADM';
	}

	public function create(Usuario $user, $resource = null)
	{
		return (string) $user->nivel_usu === 'ADM';
	}

	public function update(Usuario $user, $resource = null)
	{
		return (string) $user->nivel_usu === 'ADM';
	}

	public function delete(Usuario $user, $resource = null)
	{
		return (string) $user->nivel_usu === 'ADM';
	}
}
