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

if (!function_exists('formatarProcesso')) {
	function formatarProcesso(string $processo): string
	{
		if (preg_match('/^\d{2}\.\d{2}\.\d{4}\.\d{3}\.\d{5}-\d$/', $processo)) {
			return $processo;
		}

		if (preg_match('/^\d{7}-\d{2}\.\d{4}\.\d\.\d{2}\.\d{4}$/', $processo)) {
			return $processo;
		}

		$numero = preg_replace('/\D/', '', $processo);
		if (!is_string($numero)) {
			return $processo;
		}

		if (strlen($numero) === 17) {
			return preg_replace(
				'/(\d{2})(\d{2})(\d{4})(\d{3})(\d{5})(\d)/',
				'$1.$2.$3.$4.$5-$6',
				$numero
			) ?? $processo;
		}

		if (strlen($numero) === 20) {
			return preg_replace(
				'/(\d{7})(\d{2})(\d{4})(\d)(\d{2})(\d{4})/',
				'$1-$2.$3.$4.$5.$6',
				$numero
			) ?? $processo;
		}

		return $processo;
	}
}
