<?php

declare(strict_types=1);

namespace App\Support;

class Env
{
	/** @var bool */
	private static $loaded = false;

	public static function load($filePath)
	{
		if (self::$loaded || !is_string($filePath) || !is_file($filePath)) {
			self::$loaded = true;
			return;
		}

		$lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if (!is_array($lines)) {
			self::$loaded = true;
			return;
		}

		foreach ($lines as $line) {
			$line = trim($line);
			if ($line === '' || strpos($line, '#') === 0) {
				continue;
			}

			$position = strpos($line, '=');
			if ($position === false) {
				continue;
			}

			$key = trim(substr($line, 0, $position));
			$value = trim(substr($line, $position + 1));

			if ($key === '') {
				continue;
			}

			$value = self::normalizeValue($value);
			$_ENV[$key] = $value;
			$_SERVER[$key] = $value;
			putenv($key . '=' . $value);
		}

		self::$loaded = true;
	}

	public static function get(string $key, $default = null)
	{
		$value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

		if ($value === false || $value === null || $value === '') {
			return $default;
		}

		$normalized = strtolower((string) $value);
		if ($normalized === 'true') {
			return true;
		}

		if ($normalized === 'false') {
			return false;
		}

		if ($normalized === 'null') {
			return null;
		}

		return $value;
	}

	private static function normalizeValue($value)
	{
		if (!is_string($value)) {
			return $value;
		}

		$length = strlen($value);
		if ($length >= 2) {
			$first = substr($value, 0, 1);
			$last = substr($value, -1);
			if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
				return substr($value, 1, -1);
			}
		}

		return $value;
	}
}
