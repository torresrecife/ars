<?php

namespace Tests\Unit;

use App\Domain\Metrics\PerformanceMetricFormatter;
use Tests\TestCase;

class PerformanceMetricFormatterTest extends TestCase
{
    public function test_percent_and_icons_follow_expected_ranges()
    {
        $formatter = new PerformanceMetricFormatter();

        $this->assertSame(80.0, $formatter->percent(80, 100, 0));
        $this->assertSame(80.0, $formatter->percent(80, 100, 1));
        $this->assertSame('circle_yellow.png', $formatter->percentIcon(80));
        $this->assertSame('circle_green.png', $formatter->percentIcon(100));
        $this->assertSame('circle_blue.png', $formatter->percentIcon(110));
        $this->assertSame('circle_red.png', $formatter->percentIcon(50));
        $this->assertSame('circle_grey.png', $formatter->percentIcon(0));
    }

    public function test_heat_color_follows_expected_ranges()
    {
        $formatter = new PerformanceMetricFormatter();

        $this->assertSame('#F0F0F0', $formatter->heatColor(0));
        $this->assertSame('red', $formatter->heatColor(50));
        $this->assertSame('#FFB90F', $formatter->heatColor(90));
        $this->assertSame('green', $formatter->heatColor(100));
        $this->assertSame('#1C86EE', $formatter->heatColor(120));
    }
}
