<?php

declare(strict_types=1);

namespace App\ViewModels;

class MainPageUserContext
{
	/** @var int */
	private $sectorId;

	/** @var string */
	private $clientIds;

	/** @var string */
	private $regionMode;

	/** @var string */
	private $regionIds;

	/** @var string */
	private $regionUfs;

	/** @var string */
	private $level;

	/** @var int */
	private $id;

	/** @var array */
	private $regions;

	/** @var bool */
	private $showRegionSelector;

	public function __construct($sectorId, $clientIds, $regionMode, $regionIds, $regionUfs, $level, $id, array $regions, $showRegionSelector)
	{
		$this->sectorId = (int) $sectorId;
		$this->clientIds = (string) $clientIds;
		$this->regionMode = (string) $regionMode;
		$this->regionIds = (string) $regionIds;
		$this->regionUfs = (string) $regionUfs;
		$this->level = (string) $level;
		$this->id = (int) $id;
		$this->regions = $regions;
		$this->showRegionSelector = (bool) $showRegionSelector;
	}

	public function sectorId()
	{
		return $this->sectorId;
	}

	public function clientIds()
	{
		return $this->clientIds;
	}

	public function regionMode()
	{
		return $this->regionMode;
	}

	public function regionIds()
	{
		return $this->regionIds;
	}

	public function regionUfs()
	{
		return $this->regionUfs;
	}

	public function level()
	{
		return $this->level;
	}

	public function id()
	{
		return $this->id;
	}

	public function regions()
	{
		return $this->regions;
	}

	public function showRegionSelector()
	{
		return $this->showRegionSelector;
	}

	public function canAdmin()
	{
		return in_array($this->level, array('ADM', 'GER'), true);
	}

	public function toArray()
	{
		return array(
			'sectorId' => $this->sectorId,
			'clientIds' => $this->clientIds,
			'regionMode' => $this->regionMode,
			'regionIds' => $this->regionIds,
			'regionUfs' => $this->regionUfs,
			'level' => $this->level,
			'id' => $this->id,
			'regions' => $this->regions,
			'showRegionSelector' => $this->showRegionSelector,
		);
	}
}
