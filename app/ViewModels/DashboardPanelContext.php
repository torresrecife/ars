<?php

declare(strict_types=1);

namespace App\ViewModels;

class DashboardPanelContext
{
	/** @var int */
	private $bankId;

	/** @var string */
	private $areaId;

	/** @var int */
	private $month;

	/** @var int */
	private $year;

	/** @var int */
	private $userId;

	/** @var string */
	private $userLevel;

	/** @var string */
	private $userRegionMode;

	/** @var int */
	private $selectedRegionId;

	public function __construct($bankId, $areaId, $month, $year, $userId, $userLevel, $userRegionMode, $selectedRegionId)
	{
		$this->bankId = (int) $bankId;
		$this->areaId = (string) $areaId;
		$this->month = (int) $month;
		$this->year = (int) $year;
		$this->userId = (int) $userId;
		$this->userLevel = (string) $userLevel;
		$this->userRegionMode = (string) $userRegionMode;
		$this->selectedRegionId = (int) $selectedRegionId;
	}

	public function bankId()
	{
		return $this->bankId;
	}

	public function areaId()
	{
		return $this->areaId;
	}

	public function month()
	{
		return $this->month;
	}

	public function year()
	{
		return $this->year;
	}

	public function userId()
	{
		return $this->userId;
	}

	public function userLevel()
	{
		return $this->userLevel;
	}

	public function userRegionMode()
	{
		return $this->userRegionMode;
	}

	public function selectedRegionId()
	{
		return $this->selectedRegionId;
	}
}
