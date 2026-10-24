<?php

declare(strict_types=1);

namespace App\ViewModels;

class MonthlyProductionTotals
{
	/** @var array */
	private $weeks;

	/** @var float */
	private $meta;

	/** @var float */
	private $real;

	/** @var float */
	private $prejudice;
	private $final;

	/** @var float */
	private $percent;

	/** @var string */
	private $icon;

	public function __construct(array $weeks, $meta, $real, $prejudice, $final, $percent, $icon)
	{
		$this->weeks = $weeks;
		$this->meta = (float) $meta;
		$this->real = (float) $real;
		$this->prejudice = (float) $prejudice;
		$this->final = (float) $final;
		$this->percent = (float) $percent;
		$this->icon = (string) $icon;
	}

	public function toArray()
	{
		return array(
			'weeks' => $this->normalizeList($this->weeks),
			'meta' => $this->meta,
			'real' => $this->real,
			'prejudice' => $this->prejudice,
			'final' => $this->final,
			'percent' => $this->percent,
			'icon' => $this->icon,
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
