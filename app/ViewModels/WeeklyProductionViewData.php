<?php

declare(strict_types=1);

namespace App\ViewModels;

class WeeklyProductionViewData
{
	/** @var array */
	private $payload;

	public function __construct(array $payload)
	{
		$this->payload = $payload;
	}

	public function toArray()
	{
		return $this->payload;
	}
}
