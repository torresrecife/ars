<?php

declare(strict_types=1);

namespace App\ViewModels;

class NeoDetailContext
{
	/** @var array */
	private $typeNames;

	/** @var array */
	private $carteiraCodes;

	/** @var string */
	private $carteiraMode;

	/** @var int */
	private $month;

	/** @var int */
	private $year;

	/** @var array */
	private $week;

	/** @var array */
	private $ufCodes;

	public function __construct(array $typeNames, array $carteiraCodes, $carteiraMode, $month, $year, array $week, array $ufCodes)
	{
		$this->typeNames = $typeNames;
		$this->carteiraCodes = $carteiraCodes;
		$this->carteiraMode = (string) $carteiraMode;
		$this->month = (int) $month;
		$this->year = (int) $year;
		$this->week = $week;
		$this->ufCodes = $ufCodes;
	}

	public function typeNames()
	{
		return $this->typeNames;
	}

	public function carteiraCodes()
	{
		return $this->carteiraCodes;
	}

	public function carteiraMode()
	{
		return $this->carteiraMode;
	}

	public function month()
	{
		return $this->month;
	}

	public function year()
	{
		return $this->year;
	}

	public function week()
	{
		return $this->week;
	}

	public function ufCodes()
	{
		return $this->ufCodes;
	}
}
