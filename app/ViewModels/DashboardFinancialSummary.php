<?php

declare(strict_types=1);

namespace App\ViewModels;

class DashboardFinancialSummary
{
	/** @var array */
	private $weekTotals;

	/** @var float */
	private $metaTotal;

	/** @var float */
	private $realTotal;

	/** @var float */
	private $grandPercent;

	/** @var string */
	private $grandIcon;

	/** @var float */
	private $netRealTotal;

	/** @var float */
	private $netPercent;

	/** @var string */
	private $netIcon;

	public function __construct(array $weekTotals, $metaTotal, $realTotal, $grandPercent, $grandIcon, $netRealTotal, $netPercent, $netIcon)
	{
		$this->weekTotals = $weekTotals;
		$this->metaTotal = (float) $metaTotal;
		$this->realTotal = (float) $realTotal;
		$this->grandPercent = (float) $grandPercent;
		$this->grandIcon = (string) $grandIcon;
		$this->netRealTotal = (float) $netRealTotal;
		$this->netPercent = (float) $netPercent;
		$this->netIcon = (string) $netIcon;
	}

	public function toArray()
	{
		return array(
			'weekTotals' => $this->normalizeList($this->weekTotals),
			'metaTotal' => $this->metaTotal,
			'realTotal' => $this->realTotal,
			'grandPercent' => $this->grandPercent,
			'grandIcon' => $this->grandIcon,
			'netRealTotal' => $this->netRealTotal,
			'netPercent' => $this->netPercent,
			'netIcon' => $this->netIcon,
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
