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

	protected function prepareForValidation()
	{
		$bancoId = $this->input('banco_id', $this->input('startBanco'));
		$metaMes = $this->input('meta_mes', $this->input('mes'));
		$metaAno = $this->input('meta_ano', $this->input('ano'));
		$numes = $this->input('numes');

		if ($numes === null || $numes === '') {
			$numes = 0;
			foreach ((array) $this->all() as $key => $value) {
				if (strpos((string) $key, 'meta_name_') === 0 && trim((string) $value) !== '') {
					$numes++;
				}
			}
		}

		$this->merge(array(
			'banco_id' => $bancoId,
			'meta_mes' => $metaMes,
			'meta_ano' => $metaAno,
			'numes' => $numes,
		));
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

	public function messages()
	{
		return array(
			'banco_id.required' => 'Selecione o cliente.',
			'banco_id.integer' => 'Cliente invalido.',
			'banco_id.min' => 'Cliente invalido.',
			'meta_mes.required' => 'Mes invalido.',
			'meta_mes.integer' => 'Mes invalido.',
			'meta_mes.min' => 'Mes invalido.',
			'meta_mes.max' => 'Mes invalido.',
			'meta_ano.required' => 'Ano invalido.',
			'meta_ano.integer' => 'Ano invalido.',
			'meta_ano.min' => 'Ano invalido.',
			'meta_ano.max' => 'Ano invalido.',
			'numes.required' => 'Selecione ao menos uma meta.',
			'numes.integer' => 'Selecione ao menos uma meta.',
			'numes.min' => 'Selecione ao menos uma meta.',
			'numes.max' => 'Quantidade de metas invalida.',
		);
	}
}
