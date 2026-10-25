<?php

declare(strict_types=1);

namespace App\Domain\Metrics;

class FinancialResultCalculator
{
	/** @var PerformanceMetricFormatter */
	private $formatter;

	public function __construct(PerformanceMetricFormatter $formatter)
	{
		$this->formatter = $formatter;
	}

	public function calculate($meta, $realized, $prejudice, $precision = 1)
	{
		$meta = (float) $meta;
		$realized = (float) $realized;
		$prejudice = (float) $prejudice;
		$final = $realized - $prejudice;
		$percent = $this->formatter->percent($final, $meta, $precision);

		return array(
			'meta' => $meta,
			'realized' => $realized,
			'prejudice' => $prejudice,
			'final' => $final,
			'percent' => $percent,
			'icon' => $this->formatter->percentIcon($percent),
		);
	}
}
