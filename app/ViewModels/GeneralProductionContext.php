<?php

declare(strict_types=1);

namespace App\ViewModels;

class GeneralProductionContext
{
	/** @var string */
	private $startDate;

	/** @var string */
	private $startSector;

	/** @var int */
	private $month;

	/** @var int */
	private $year;

	/** @var int */
	private $userSectorId;

	/** @var string */
	private $userClientIds;

	/** @var int */
	private $userId;

	/** @var string */
	private $userLevel;

	/** @var string */
	private $userRegionMode;

	/** @var int */
	private $selectedRegionId;

	public function __construct($startDate, $startSector, $month, $year, $userSectorId, $userClientIds, $userId, $userLevel, $userRegionMode, $selectedRegionId)
	{
		$this->startDate = (string) $startDate;
		$this->startSector = (string) $startSector;
		$this->month = (int) $month;
		$this->year = (int) $year;
		$this->userSectorId = (int) $userSectorId;
		$this->userClientIds = (string) $userClientIds;
		$this->userId = (int) $userId;
		$this->userLevel = (string) $userLevel;
		$this->userRegionMode = (string) $userRegionMode;
		$this->selectedRegionId = (int) $selectedRegionId;
	}

	public function startDate()
	{
		return $this->startDate;
	}

	public function startSector()
	{
		return $this->startSector;
	}

	public function month()
	{
		return $this->month;
	}

	public function year()
	{
		return $this->year;
	}

	public function userSectorId()
	{
		return $this->userSectorId;
	}

	public function userClientIds()
	{
		return $this->userClientIds;
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
