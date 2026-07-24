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

	/** @var string */
	private $href;

	public function __construct($className, $label, $javascript = '', $href = '')
	{
		$this->className = (string) $className;
		$this->label = (string) $label;
		$this->javascript = (string) $javascript;
		$this->href = (string) $href;
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

	public function href()
	{
		return $this->href;
	}

	public function toArray()
	{
		return array(
			'class' => $this->className,
			'label' => $this->label,
			'js' => $this->javascript,
			'href' => $this->href,
		);
	}
}
