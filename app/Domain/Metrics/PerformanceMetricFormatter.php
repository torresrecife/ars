<?php

declare(strict_types=1);

namespace App\Domain\Metrics;

class PerformanceMetricFormatter
{
	public function percent($real, $meta, $precision = 0)
	{
		if ((float) $meta == 0.0) {
			return 0.0;
		}

		return (float) number_format((((float) $real / (float) $meta) * 100), (int) $precision, '.', '');
	}

	public function percentIcon($realOrPercent, $meta = null)
	{
		$percent = $meta === null
			? (float) $realOrPercent
			: $this->percent($realOrPercent, $meta);

		if (($meta !== null && (float) $meta == 0.0) || $percent == 0.0) {
			return 'circle_grey.png';
		}
		if ($percent >= 100 && $percent < 110) {
			return 'circle_green.png';
		}
		if ($percent < 100 && $percent >= 80) {
			return 'circle_yellow.png';
		}
		if ($percent >= 110) {
			return 'circle_blue.png';
		}

		return 'circle_red.png';
	}

	public function heatColor($percent)
	{
		if ((float) $percent == 0.0) {
			return '#F0F0F0';
		}
		if ((float) $percent < 80) {
			return 'red';
		}
		if ((float) $percent < 100) {
			return '#FFB90F';
		}
		if ((float) $percent < 110) {
			return 'green';
		}

		return '#1C86EE';
	}
}
