<?php

declare(strict_types=1);

namespace App\Http\Requests;

class ClientUpdateRequest extends ClientStoreRequest
{
	public function rules()
	{
		$rules = parent::rules();
		$rules['banco_id'] = 'required|integer|min:1';

		return $rules;
	}
}
