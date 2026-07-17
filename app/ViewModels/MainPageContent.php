<?php

declare(strict_types=1);

namespace App\ViewModels;

class MainPageContent
{
	/** @var string */
	private $type;

	/** @var string */
	private $view;

	/** @var array */
	private $data;

	/** @var string */
	private $controller;

	/** @var bool */
	private $spacer;

	private function __construct($type, $view, array $data, $controller, $spacer)
	{
		$this->type = (string) $type;
		$this->view = (string) $view;
		$this->data = $data;
		$this->controller = (string) $controller;
		$this->spacer = (bool) $spacer;
	}

	public static function forView($view, array $data)
	{
		return new self('view', $view, $data, '', false);
	}

	public static function forController($controller, $spacer = false)
	{
		return new self('controller', '', array(), $controller, $spacer);
	}

	public function type()
	{
		return $this->type;
	}

	public function view()
	{
		return $this->view;
	}

	public function data()
	{
		return $this->data;
	}

	public function controller()
	{
		return $this->controller;
	}

	public function spacer()
	{
		return $this->spacer;
	}

	public function toArray()
	{
		$payload = array('type' => $this->type);

		if ($this->type === 'view') {
			$payload['view'] = $this->view;
			$payload['data'] = $this->data;

			return $payload;
		}

		$payload['controller'] = $this->controller;
		if ($this->spacer) {
			$payload['spacer'] = true;
		}

		return $payload;
	}
}
