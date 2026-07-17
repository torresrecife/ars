<?php

declare(strict_types=1);

namespace App\ViewModels;

class MainPageTopAction
{
	/** @var string */
	private $className;

	/** @var string */
	private $label;

	/** @var string */
	private $javascript;

	public function __construct($className, $label, $javascript)
	{
		$this->className = (string) $className;
		$this->label = (string) $label;
		$this->javascript = (string) $javascript;
	}

	public function className()
	{
		return $this->className;
	}

	public function label()
	{
		return $this->label;
	}

	public function javascript()
	{
		return $this->javascript;
	}

	public function toArray()
	{
		return array(
			'class' => $this->className,
			'label' => $this->label,
			'js' => $this->javascript,
		);
	}
}
