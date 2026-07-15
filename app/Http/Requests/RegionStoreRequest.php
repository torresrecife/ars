<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegionStoreRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return array(
			'regiao_nome' => 'required|string|max:100',
			'regiao_slug' => 'nullable|string|max:100|regex:/^[A-Za-z0-9\\-\\s_]+$/',
			'regiao_status' => 'nullable|in:Y,N',
			'regiao_ufs' => 'nullable|string|regex:/^[A-Za-z,\\s]*$/',
		);
	}
}
