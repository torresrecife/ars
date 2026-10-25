<?php

namespace Tests\Unit;

use App\Domain\Metrics\FinancialResultCalculator;
use App\Domain\Metrics\PerformanceMetricFormatter;
use Tests\TestCase;

class FinancialResultCalculatorTest extends TestCase
{
	public function test_calculates_final_value_and_percentage_from_gross_realized_value()
	{
		$result = (new FinancialResultCalculator(new PerformanceMetricFormatter()))
			->calculate(510714.36, 414647.04, 34807.33, 1);

		$this->assertEqualsWithDelta(379839.71, $result['final'], 0.001);
		$this->assertEqualsWithDelta(74.4, $result['percent'], 0.001);
		$this->assertSame('circle_red.png', $result['icon']);
	}

	public function test_keeps_realized_value_unchanged_when_there_is_no_prejudice()
	{
		$result = (new FinancialResultCalculator(new PerformanceMetricFormatter()))
			->calculate(100, 80, 0, 1);

		$this->assertSame(80.0, $result['realized']);
		$this->assertSame(80.0, $result['final']);
		$this->assertSame(80.0, $result['percent']);
	}
}
