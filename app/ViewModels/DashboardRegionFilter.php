<?php

declare(strict_types=1);

namespace App\ViewModels;

class DashboardRegionFilter
{
	/** @var int */
	private $selectedRegionId;

	/** @var array */
	private $ufs;

	/** @var string */
	private $label;

	/** @var array */
	private $metaRegionIds;

	public function __construct($selectedRegionId, array $ufs, $label, array $metaRegionIds)
	{
		$this->selectedRegionId = (int) $selectedRegionId;
		$this->ufs = $ufs;
		$this->label = (string) $label;
		$this->metaRegionIds = $metaRegionIds;
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

	public function metaRegionIds()
	{
		return $this->metaRegionIds;
	}
}
