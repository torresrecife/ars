<?php

declare(strict_types=1);

namespace App\Data;

class GeneralProductionInput
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
			'startDate' => isset($payload['startDate']) ? $payload['startDate'] : date('M'),
			'startSetor' => isset($payload['startSetor']) ? $payload['startSetor'] : '',
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
