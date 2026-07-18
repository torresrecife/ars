<?php

declare(strict_types=1);

namespace App\ViewModels;

class DashboardMetricCell
{
	/** @var float */
	private $meta;

	/** @var float */
	private $real;

	/** @var float */
	private $percent;

	/** @var string */
	private $icon;

	/** @var array */
	private $codes;

	public function __construct($meta, $real, $percent, $icon, array $codes = array())
	{
		$this->meta = (float) $meta;
		$this->real = (float) $real;
		$this->percent = (float) $percent;
		$this->icon = (string) $icon;
		$this->codes = array_values($codes);
	}

	public function toArray()
	{
		return array(
			'meta' => $this->meta,
			'real' => $this->real,
			'percent' => $this->percent,
			'icon' => $this->icon,
			'codes' => $this->codes,
		);
	}
}
