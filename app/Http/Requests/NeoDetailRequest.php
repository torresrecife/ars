<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NeoDetailRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return array(
			'codig_and' => array('nullable', 'string'),
			'banco_and' => array('nullable', 'string'),
			'codig_lnc' => array('nullable', 'string'),
			'banco_lnc' => array('nullable', 'string'),
			'detail_bank_id' => array('nullable', 'integer', 'min:0'),
			'detail_anda_id' => array('nullable', 'integer', 'min:0'),
			'detail_month' => array('nullable', 'integer', 'between:1,12'),
			'detail_year' => array('nullable', 'integer', 'min:2000', 'max:2100'),
			'detail_week' => array('nullable'),
			'detail_region_id' => array('nullable', 'integer', 'min:0'),
		);
	}
}
