<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DashboardPanelRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return array(
			'bank_id' => array('nullable', 'integer', 'min:0'),
			'area_id' => array('nullable', 'string'),
			'mes' => array('nullable', 'integer', 'between:1,12'),
			'ano' => array('nullable', 'integer', 'min:2000', 'max:2100'),
			'regiao_id' => array('nullable', 'integer', 'min:0'),
		);
	}
}
