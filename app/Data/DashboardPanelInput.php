<?php

declare(strict_types=1);

namespace App\Data;

class DashboardPanelInput
{
	/** @var array */
	private $payload;

	private function __construct(array $payload)
	{
		$this->payload = $payload;
	}

	public static function fromArray(array $payload)
	{
		return new self(array(
			'bank_id' => isset($payload['bank_id']) ? $payload['bank_id'] : 0,
			'area_id' => isset($payload['area_id']) ? $payload['area_id'] : '',
			'mes' => isset($payload['mes']) ? $payload['mes'] : date('m'),
			'ano' => isset($payload['ano']) ? $payload['ano'] : date('Y'),
			'regiao_id' => isset($payload['regiao_id']) ? $payload['regiao_id'] : 0,
		));
	}

	public function toArray()
	{
		return $this->payload;
	}
}
