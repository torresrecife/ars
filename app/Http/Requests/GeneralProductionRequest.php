<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralProductionRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return array(
			'startDate' => array('nullable', 'string'),
			'startSetor' => array('nullable', 'string'),
			'mes' => array('nullable', 'integer', 'between:1,12'),
			'ano' => array('nullable', 'integer', 'min:2000', 'max:2100'),
			'regiao_id' => array('nullable', 'integer', 'min:0'),
		);
	}
}
