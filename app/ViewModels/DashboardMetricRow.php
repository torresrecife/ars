<?php

declare(strict_types=1);

namespace App\ViewModels;

class DashboardMetricRow
{
	/** @var int */
	private $andaId;

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

	/** @var array */
	private $totalCodes;

	public function __construct($andaId, $name, array $weekData, $totalMeta, $totalReal, $totalPercent, $totalIcon, array $totalCodes)
	{
		$this->andaId = (int) $andaId;
		$this->name = (string) $name;
		$this->weekData = $weekData;
		$this->totalMeta = (float) $totalMeta;
		$this->totalReal = (float) $totalReal;
		$this->totalPercent = (float) $totalPercent;
		$this->totalIcon = (string) $totalIcon;
		$this->totalCodes = array_values($totalCodes);
	}

	public function toArray()
	{
		return array(
			'andaId' => $this->andaId,
			'name' => $this->name,
			'weekData' => $this->normalizeList($this->weekData),
			'totalMeta' => $this->totalMeta,
			'totalReal' => $this->totalReal,
			'totalPercent' => $this->totalPercent,
			'totalIcon' => $this->totalIcon,
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
