<?php

declare(strict_types=1);

namespace App\Support;

class Config
{
	/** @var array */
	private $items = array();

	public function __construct(string $basePath)
	{
		$configPath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'config';
		$this->items = $this->loadDirectory($configPath);
	}

	public function all(): array
	{
		return $this->items;
	}

	public function get(string $key, $default = null)
	{
		$segments = explode('.', $key);
		$value = $this->items;

		foreach ($segments as $segment) {
			if (!is_array($value) || !array_key_exists($segment, $value)) {
				return $default;
			}

			$value = $value[$segment];
		}

		return $value;
	}

	private function loadDirectory(string $configPath): array
	{
		if (!is_dir($configPath)) {
			return array();
		}

		$items = array();
		foreach (glob($configPath . DIRECTORY_SEPARATOR . '*.php') ?: array() as $file) {
			$key = pathinfo($file, PATHINFO_FILENAME);
			$data = require $file;
			$items[$key] = is_array($data) ? $data : array();
		}

		return $items;
	}
}
