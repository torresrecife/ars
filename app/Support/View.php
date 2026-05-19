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
		$viewFile = $this->basePath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php';
		if (!is_file($viewFile)) {
			return '';
		}

		extract($data, EXTR_SKIP);

		ob_start();
		include $viewFile;
		return (string) ob_get_clean();
	}
}
