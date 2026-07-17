<?php

declare(strict_types=1);

namespace App\ViewModels;

class GeneralProductionRegionFilter
{
	/** @var int */
	private $selectedRegionId;

	/** @var array */
	private $ufs;

	/** @var string */
	private $label;

	public function __construct($selectedRegionId, array $ufs, $label)
	{
		$this->selectedRegionId = (int) $selectedRegionId;
		$this->ufs = $ufs;
		$this->label = (string) $label;
	}

	public function selectedRegionId()
	{
		return $this->selectedRegionId;
	}

	public function ufs()
	{
		return $this->ufs;
	}

	public function label()
	{
		return $this->label;
	}
}
