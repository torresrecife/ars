<?php

declare(strict_types=1);

namespace App\ViewModels;

class FinancialDetailViewData
{
	/** @var array */
	private $rows;

	/** @var string */
	private $bankName;

	/** @var int */
	private $totalCount;

	/** @var float */
	private $totalValue;

	public function __construct(array $rows, $bankName, $totalCount, $totalValue)
	{
		$this->rows = $rows;
		$this->bankName = (string) $bankName;
		$this->totalCount = (int) $totalCount;
		$this->totalValue = (float) $totalValue;
	}

	public function toArray()
	{
		return array(
			'rows' => $this->normalizeRows($this->rows),
			'bankName' => $this->bankName,
			'totalCount' => $this->totalCount,
			'totalValue' => $this->totalValue,
		);
	}

	private function normalizeRows(array $rows)
	{
		$normalized = array();
		foreach ($rows as $row) {
			$normalized[] = is_object($row) && method_exists($row, 'toArray') ? $row->toArray() : $row;
		}

		return $normalized;
	}
}
