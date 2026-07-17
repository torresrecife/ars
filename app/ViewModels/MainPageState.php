<?php

declare(strict_types=1);

namespace App\ViewModels;

class MainPageState
{
	/** @var string */
	private $section;

	/** @var string */
	private $areaId;

	/** @var string */
	private $bankId;

	/** @var int */
	private $geral;

	/** @var int */
	private $regiaoId;

	/** @var int */
	private $mes;

	/** @var int */
	private $ano;

	/** @var string */
	private $startDate;

	/** @var string */
	private $startSetor;

	public function __construct($section, $areaId, $bankId, $geral, $regiaoId, $mes, $ano, $startDate, $startSetor)
	{
		$this->section = (string) $section;
		$this->areaId = (string) $areaId;
		$this->bankId = (string) $bankId;
		$this->geral = (int) $geral;
		$this->regiaoId = (int) $regiaoId;
		$this->mes = (int) $mes;
		$this->ano = (int) $ano;
		$this->startDate = (string) $startDate;
		$this->startSetor = (string) $startSetor;
	}

	public function section()
	{
		return $this->section;
	}

	public function areaId()
	{
		return $this->areaId;
	}

	public function bankId()
	{
		return $this->bankId;
	}

	public function geral()
	{
		return $this->geral;
	}

	public function regiaoId()
	{
		return $this->regiaoId;
	}

	public function mes()
	{
		return $this->mes;
	}

	public function ano()
	{
		return $this->ano;
	}

	public function startDate()
	{
		return $this->startDate;
	}

	public function startSetor()
	{
		return $this->startSetor;
	}

	public function toArray()
	{
		return array(
			'section' => $this->section,
			'area_id' => $this->areaId,
			'bank_id' => $this->bankId,
			'geral' => $this->geral,
			'regiao_id' => $this->regiaoId,
			'mes' => $this->mes,
			'ano' => $this->ano,
			'startDate' => $this->startDate,
			'startSetor' => $this->startSetor,
		);
	}
}
