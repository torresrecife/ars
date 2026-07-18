<?php

declare(strict_types=1);

namespace App\ViewModels;

class DashboardPrejudiceRow
{
	/** @var int */
	private $andaId;

	/** @var string */
	private $name;

	/** @var array */
	private $weekData;

	/** @var float */
	private $totalReal;

	/** @var array */
	private $totalCodes;

	public function __construct($andaId, $name, array $weekData, $totalReal, array $totalCodes)
	{
		$this->andaId = (int) $andaId;
		$this->name = (string) $name;
		$this->weekData = $weekData;
		$this->totalReal = (float) $totalReal;
		$this->totalCodes = array_values($totalCodes);
	}

	public function toArray()
	{
		return array(
			'andaId' => $this->andaId,
			'name' => $this->name,
			'weekData' => $this->normalizeList($this->weekData),
			'totalReal' => $this->totalReal,
			'totalCodes' => $this->totalCodes,
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
