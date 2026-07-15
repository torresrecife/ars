<?php

declare(strict_types=1);

namespace App\Http\Requests;

class MetaUpdateRequest extends MetaStoreRequest
{
	public function rules()
	{
		$rules = parent::rules();
		$rules['meta_id'] = 'required|integer|min:1';

		return $rules;
	}
}
