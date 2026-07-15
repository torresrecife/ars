<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientStoreRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return array(
			'banco_name' => 'required|string|max:255',
			'banco_cod' => 'required|string|max:255',
			'banco_area' => 'required|integer|min:1',
			'banco_status' => 'required|in:Y,N',
			'banco_class' => 'nullable|string|max:255',
			'simulador' => 'nullable|integer|min:0',
			'banco_curto' => 'nullable|string|max:255',
			'dados_json' => 'nullable|string',
			'cartei_num' => 'nullable|integer|min:0',
		);
	}
}
