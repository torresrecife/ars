<?php

declare(strict_types=1);

namespace App\ViewModels;

class WeeklyProductionRow
{
	/** @var string */
	private $name;

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

	/** @var array */
	private $codes;

	public function __construct($name, $metaMonth, $metaToday, $realized, $balance, $percentToday, $percentMonth, $color, array $codes)
	{
		$this->name = (string) $name;
		$this->metaMonth = (float) $metaMonth;
		$this->metaToday = (float) $metaToday;
		$this->realized = (float) $realized;
		$this->balance = (float) $balance;
		$this->percentToday = (float) $percentToday;
		$this->percentMonth = (float) $percentMonth;
		$this->color = (string) $color;
		$this->codes = array_values($codes);
	}

	public function toArray()
	{
		return array(
			'name' => $this->name,
			'metaMonth' => $this->metaMonth,
			'metaToday' => $this->metaToday,
			'realized' => $this->realized,
			'balance' => $this->balance,
			'percentToday' => $this->percentToday,
			'percentMonth' => $this->percentMonth,
			'color' => $this->color,
			'codes' => $this->codes,
		);
	}
}
