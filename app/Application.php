<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\Database\DatabaseManager;
use App\Support\Config;

class Application
{
	/** @var string */
	private $basePath;

	/** @var Config */
	private $config;

	/** @var DatabaseManager */
	private $databaseManager;

	public function __construct(string $basePath)
	{
		$this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
		$this->config = new Config($this->basePath);
		$this->databaseManager = new DatabaseManager($this->config);
	}

	public function basePath(string $path = ''): string
	{
		if ($path === '') {
			return $this->basePath;
		}

		return $this->basePath . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
	}

	public function config(): Config
	{
		return $this->config;
	}

	public function db(): DatabaseManager
	{
		return $this->databaseManager;
	}
}
