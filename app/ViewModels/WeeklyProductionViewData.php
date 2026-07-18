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
		return $this->normalize($this->payload);
	}

	private function normalize($value)
	{
		if (is_object($value) && method_exists($value, 'toArray')) {
			return $this->normalize($value->toArray());
		}

		if (!is_array($value)) {
			return $value;
		}

		$normalized = array();
		foreach ($value as $key => $item) {
			$normalized[$key] = $this->normalize($item);
		}

		return $normalized;
	}
}
