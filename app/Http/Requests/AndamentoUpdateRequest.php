<?php

declare(strict_types=1);

namespace App\Http\Requests;

class AndamentoUpdateRequest extends AndamentoStoreRequest
{
	public function rules()
	{
		$rules = parent::rules();
		$rules['anda_id'] = 'required|integer|min:1';

		return $rules;
	}
}
