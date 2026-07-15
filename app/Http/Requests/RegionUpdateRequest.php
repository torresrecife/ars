<?php

declare(strict_types=1);

namespace App\Http\Requests;

class RegionUpdateRequest extends RegionStoreRequest
{
	public function rules()
	{
		$rules = parent::rules();
		$rules['regiao_id'] = 'required|integer|min:1';

		return $rules;
	}
}
