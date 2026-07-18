<?php

declare(strict_types=1);

namespace App\ViewModels;

class MonthlyProductionRow
{
	/** @var string */
	private $name;

	/** @var array */
	private $weekData;

	/** @var float */
	private $totalMeta;

	/** @var float */
	private $totalReal;

	/** @var float */
	private $totalPercent;

	/** @var string */
	private $totalIcon;

	public function __construct($name, array $weekData, $totalMeta, $totalReal, $totalPercent, $totalIcon)
	{
		$this->name = (string) $name;
		$this->weekData = $weekData;
		$this->totalMeta = (float) $totalMeta;
		$this->totalReal = (float) $totalReal;
		$this->totalPercent = (float) $totalPercent;
		$this->totalIcon = (string) $totalIcon;
	}

	public function toArray()
	{
		return array(
			'name' => $this->name,
			'weekData' => $this->normalizeList($this->weekData),
			'totalMeta' => $this->totalMeta,
			'totalReal' => $this->totalReal,
			'totalPercent' => $this->totalPercent,
			'totalIcon' => $this->totalIcon,
		);
	}

	private function normalizeList(array $items)
	{
		$normalized = array();
		foreach ($items as $item) {
			$normalized[] = is_object($item) && method_exists($item, 'toArray') ? $item->toArray() : $item;
		}

		return $normalized;
	}
}
