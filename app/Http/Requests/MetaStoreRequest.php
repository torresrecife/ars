<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MetaStoreRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return array(
			'banco_id' => 'required|integer|min:1',
			'meta_mes' => 'required|integer|min:1|max:12',
			'meta_ano' => 'required|integer|min:2000|max:2100',
			'numes' => 'required|integer|min:1|max:20',
		);
	}
}
