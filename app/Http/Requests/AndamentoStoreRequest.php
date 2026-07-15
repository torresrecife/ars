<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AndamentoStoreRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return array(
			'nome' => 'required|string|max:255',
			'chave' => 'required|string|max:255',
			'anda_neo' => 'nullable|string',
			'especie' => 'required|integer|in:1,2',
			'painel' => 'required|in:Y,N',
			'titulo' => 'nullable|string|max:255',
		);
	}
}
