<?php

declare(strict_types=1);

namespace App\Http\Requests;

class UserUpdateRequest extends UserStoreRequest
{
	public function rules()
	{
		$rules = parent::rules();
		$rules['id_usu'] = 'required|integer|min:1';

		return $rules;
	}
}
