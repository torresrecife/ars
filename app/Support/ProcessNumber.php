<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Formata o numero do processo vindo do NEO: CNJ (20 digitos,
 * 0000000-00.0000.0.00.0000) ou o formato antigo (17 digitos,
 * 00.00.0000.000.00000-0). Ja formatado ou fora desses tamanhos volta como veio.
 */
class ProcessNumber
{
	public static function format(string $processo): string
	{
		if (preg_match('/^\d{2}\.\d{2}\.\d{4}\.\d{3}\.\d{5}-\d$/', $processo)
			|| preg_match('/^\d{7}-\d{2}\.\d{4}\.\d\.\d{2}\.\d{4}$/', $processo)) {
			return $processo;
		}

		$numero = preg_replace('/\D/', '', $processo);
		if (!is_string($numero)) {
			return $processo;
		}

		if (strlen($numero) === 17) {
			return preg_replace('/(\d{2})(\d{2})(\d{4})(\d{3})(\d{5})(\d)/', '$1.$2.$3.$4.$5-$6', $numero) ?? $processo;
		}

		if (strlen($numero) === 20) {
			return preg_replace('/(\d{7})(\d{2})(\d{4})(\d)(\d{2})(\d{4})/', '$1-$2.$3.$4.$5.$6', $numero) ?? $processo;
		}

		return $processo;
	}
}
