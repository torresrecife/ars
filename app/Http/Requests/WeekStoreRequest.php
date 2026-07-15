<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeekStoreRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return array(
			'mes_sem' => 'required|integer|min:1|max:12',
			'ano_sem' => 'required|digits:4',
			'ini1_sem' => 'required|integer|min:1|max:31',
			'fim1_sem' => 'required|integer|min:1|max:31',
			'ini2_sem' => 'required|integer|min:1|max:31',
			'fim2_sem' => 'required|integer|min:1|max:31',
			'ini3_sem' => 'required|integer|min:1|max:31',
			'fim3_sem' => 'required|integer|min:1|max:31',
			'ini4_sem' => 'required|integer|min:1|max:31',
			'fim4_sem' => 'required|integer|min:1|max:31',
			'ini5_sem' => 'nullable|integer|min:0|max:31',
			'fim5_sem' => 'nullable|integer|min:0|max:31',
		);
	}
}
