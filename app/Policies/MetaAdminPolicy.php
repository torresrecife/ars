<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Usuario;

class MetaAdminPolicy
{
	public function viewAny(Usuario $user, $resource = null)
	{
		return in_array((string) $user->nivel_usu, array('ADM', 'GER'), true);
	}

	public function view(Usuario $user, $resource = null)
	{
		return in_array((string) $user->nivel_usu, array('ADM', 'GER'), true);
	}

	public function create(Usuario $user, $resource = null)
	{
		return in_array((string) $user->nivel_usu, array('ADM', 'GER'), true);
	}

	public function update(Usuario $user, $resource = null)
	{
		return in_array((string) $user->nivel_usu, array('ADM', 'GER'), true);
	}

	public function delete(Usuario $user, $resource = null)
	{
		return in_array((string) $user->nivel_usu, array('ADM', 'GER'), true);
	}
}
