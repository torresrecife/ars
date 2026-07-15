<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return array(
			'nome_usu' => 'required|string|max:150',
			'login_usu' => 'required|string|max:100',
			'email_usu' => 'required|email|max:150',
			'nivel_usu' => 'required|in:ADM,GER,USU',
			'setor_usu' => 'required|integer|min:0',
			'status_usu' => 'required|in:ATI,INA',
			'regiao_modo' => 'nullable|in:N,R,T',
			'banco_neo' => 'nullable|string',
			'regiao_neo' => 'nullable|string|regex:/^[0-9,]*$/',
			'senha_usu1' => 'nullable|string|min:4|same:senha_usu2',
			'senha_usu2' => 'nullable|string|min:4',
		);
	}
}
