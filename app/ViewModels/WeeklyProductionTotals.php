<?php

declare(strict_types=1);

namespace App\ViewModels;

class WeeklyProductionTotals
{
	/** @var float */
	private $metaMonth;

	/** @var float */
	private $metaToday;

	/** @var float */
	private $realized;

	/** @var float */
	private $balance;

	/** @var float */
	private $percentToday;

	/** @var float */
	private $percentMonth;

	/** @var string */
	private $color;

	/** @var string */
	private $colorClass;

	public function __construct($metaMonth, $metaToday, $realized, $balance, $percentToday, $percentMonth, $color, $colorClass = '')
	{
		$this->metaMonth = (float) $metaMonth;
		$this->metaToday = (float) $metaToday;
		$this->realized = (float) $realized;
		$this->balance = (float) $balance;
		$this->percentToday = (float) $percentToday;
		$this->percentMonth = (float) $percentMonth;
		$this->color = (string) $color;
		$this->colorClass = (string) $colorClass;
	}

	public function toArray()
	{
		return array(
			'metaMonth' => $this->metaMonth,
			'metaToday' => $this->metaToday,
			'realized' => $this->realized,
			'balance' => $this->balance,
			'percentToday' => $this->percentToday,
			'percentMonth' => $this->percentMonth,
			'color' => $this->color,
			'colorClass' => $this->colorClass,
		);
	}
}
