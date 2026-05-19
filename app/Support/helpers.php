<?php

declare(strict_types=1);

if (!function_exists('base_path')) {
	function base_path(string $path = ''): string
	{
		$basePath = __DIR__ . '/../../';
		if ($path === '') {
			return realpath($basePath) ?: $basePath;
		}

		$resolvedBase = realpath($basePath) ?: rtrim($basePath, DIRECTORY_SEPARATOR);
		return $resolvedBase . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
	}
}
