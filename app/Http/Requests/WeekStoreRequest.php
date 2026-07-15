<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeekStoreRequest extends FormRequest
{
	protected function prepareForValidation()
	{
		$this->merge(array(
			'mes_sem' => preg_replace('/\D+/', '', (string) $this->input('mes_sem', '')),
			'ano_sem' => preg_replace('/\D+/', '', (string) $this->input('ano_sem', '')),
			'ini1_sem' => preg_replace('/\D+/', '', (string) $this->input('ini1_sem', '')),
			'fim1_sem' => preg_replace('/\D+/', '', (string) $this->input('fim1_sem', '')),
			'ini2_sem' => preg_replace('/\D+/', '', (string) $this->input('ini2_sem', '')),
			'fim2_sem' => preg_replace('/\D+/', '', (string) $this->input('fim2_sem', '')),
			'ini3_sem' => preg_replace('/\D+/', '', (string) $this->input('ini3_sem', '')),
			'fim3_sem' => preg_replace('/\D+/', '', (string) $this->input('fim3_sem', '')),
			'ini4_sem' => preg_replace('/\D+/', '', (string) $this->input('ini4_sem', '')),
			'fim4_sem' => preg_replace('/\D+/', '', (string) $this->input('fim4_sem', '')),
			'ini5_sem' => preg_replace('/\D+/', '', (string) $this->input('ini5_sem', '')),
			'fim5_sem' => preg_replace('/\D+/', '', (string) $this->input('fim5_sem', '')),
		));
	}

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
