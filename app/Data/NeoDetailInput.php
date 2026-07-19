<?php

declare(strict_types=1);

namespace App\Data;

class NeoDetailInput
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
			'codig_and' => isset($payload['codig_and']) ? $payload['codig_and'] : '',
			'banco_and' => isset($payload['banco_and']) ? $payload['banco_and'] : '',
			'codig_lnc' => isset($payload['codig_lnc']) ? $payload['codig_lnc'] : '',
			'banco_lnc' => isset($payload['banco_lnc']) ? $payload['banco_lnc'] : '',
			'detail_bank_id' => isset($payload['detail_bank_id']) ? $payload['detail_bank_id'] : 0,
			'detail_anda_id' => isset($payload['detail_anda_id']) ? $payload['detail_anda_id'] : 0,
			'detail_month' => isset($payload['detail_month']) ? $payload['detail_month'] : 0,
			'detail_year' => isset($payload['detail_year']) ? $payload['detail_year'] : 0,
			'detail_week' => isset($payload['detail_week']) ? $payload['detail_week'] : 'total',
			'detail_region_id' => isset($payload['detail_region_id']) ? $payload['detail_region_id'] : 0,
		));
	}

	public function toArray()
	{
		return $this->payload;
	}

	public function bankNameForAndamento()
	{
		return (string) $this->payload['banco_and'];
	}

	public function bankNameForFinancial()
	{
		return (string) $this->payload['banco_lnc'];
	}
}
