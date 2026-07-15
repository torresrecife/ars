<?php

declare(strict_types=1);

namespace App\Support;

class View
{
	/** @var string */
	private $basePath;

	public function __construct($basePath)
	{
		$this->basePath = rtrim((string) $basePath, DIRECTORY_SEPARATOR);
	}

	public function render($view, array $data = array())
	{
		$bladeView = str_replace('/', '.', (string) $view);
		if (!function_exists('app')) {
			return '';
		}

		return app('view')->make($bladeView, $data)->render();
	}
}
