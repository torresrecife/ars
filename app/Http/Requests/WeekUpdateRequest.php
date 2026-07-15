<?php

declare(strict_types=1);

namespace App\Http\Requests;

class WeekUpdateRequest extends WeekStoreRequest
{
	public function rules()
	{
		$rules = parent::rules();
		$rules['id_sem'] = 'required|integer|min:1';

		return $rules;
	}
}
