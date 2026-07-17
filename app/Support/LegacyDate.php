<?php

declare(strict_types=1);

namespace App\Support;

class LegacyDate
{
	public static function countBusinessDaysInclusive(string $startDate, string $endDate): int
	{
		$startTimestamp = self::brDateToTimestamp($startDate);
		$endTimestamp = self::brDateToTimestamp($endDate);

		$larger = max($startTimestamp, $endTimestamp);
		$smaller = min($startTimestamp, $endTimestamp);
		$totalDays = (int) (($larger - $smaller) / 86400) + 1;
		$nonBusinessDays = 0;
		$currentDate = $startDate;

		while ($currentDate !== $endDate) {
			$weekDay = (int) date('w', self::brDateToTimestamp($currentDate));
			if ($weekDay === 0 || $weekDay === 6) {
				$nonBusinessDays++;
			} else {
				foreach (self::holidays((int) date('Y')) as $holiday) {
					if ($currentDate === $holiday) {
						$nonBusinessDays++;
						break;
					}
				}
			}

			$currentDate = self::addOneDay($currentDate);
		}

		return $totalDays - $nonBusinessDays;
	}

	/**
	 * @return array{start:string,end:string}
	 */
	public static function legacyWeekRange(int $month, int $year, int $index): array
	{
		$monthString = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
		$seed = $year . '-' . $monthString . '-01';

		$end1 = date('Y-m-d', strtotime('+' . self::legacyWeekDelta($seed) . ' days', strtotime($seed)));
		$end2 = date('Y-m-d', strtotime('+' . self::legacyWeekDelta($end1) . ' days', strtotime($end1)));
		$end3 = date('Y-m-d', strtotime('+' . self::legacyWeekDelta($end2) . ' days', strtotime($end2)));
		$end4 = date('Y-m-d', strtotime('+' . self::legacyWeekDelta($end3) . ' days', strtotime($end3)));
		$end5 = date('Y-m-d', strtotime('+' . self::legacyWeekDelta($end4) . ' days', strtotime($end4)));

		$start1 = $seed;
		$start2 = date('Y-m-d', strtotime('+' . (self::legacyWeekDelta($seed) + 1) . ' days', strtotime($seed)));
		$start3 = date('Y-m-d', strtotime('+' . (self::legacyWeekDelta($end1) + 1) . ' days', strtotime($end1)));
		$start4 = date('Y-m-d', strtotime('+' . (self::legacyWeekDelta($end2) + 1) . ' days', strtotime($end2)));
		$start5 = date('Y-m-d', strtotime('+' . (self::legacyWeekDelta($end3) + 1) . ' days', strtotime($end3)));

		$starts = array(
			1 => $start1,
			2 => $start2,
			3 => $start3,
			4 => $start4,
			5 => $start5,
		);

		$ends = array(
			1 => $end1,
			2 => $end2,
			3 => $end3,
			4 => $end4,
			5 => $year . '-' . $monthString . '-' . self::lastDayOfMonth($month, $year),
		);

		return array(
			'start' => $starts[$index],
			'end' => $ends[$index],
		);
	}

	public static function lastDayOfMonth(int $month, int $year): string
	{
		return date('t', mktime(0, 0, 0, $month, 1, $year));
	}

	private static function legacyWeekDelta(string $date): int
	{
		switch ((int) date('w', strtotime($date))) {
			case 0:
				return 6;
			case 1:
				return 5;
			case 2:
				return 4;
			case 3:
				return 3;
			case 4:
				return 2;
			case 5:
				return 8;
			case 6:
				return 7;
			default:
				return 0;
		}
	}

	private static function brDateToTimestamp(string $date): int
	{
		$year = substr($date, 6, 4);
		$month = substr($date, 3, 2);
		$day = substr($date, 0, 2);

		return mktime(0, 0, 0, (int) $month, (int) $day, (int) $year);
	}

	private static function addOneDay(string $date): string
	{
		$year = substr($date, 6, 4);
		$month = substr($date, 3, 2);
		$day = substr($date, 0, 2);

		return date('d/m/Y', mktime(0, 0, 0, (int) $month, ((int) $day) + 1, (int) $year));
	}

	/**
	 * @return string[]
	 */
	private static function holidays(int $year): array
	{
		$day = 86400;
		$easter = easter_date($year);
		$carnaval = $easter - (47 * $day);
		$sextaSanta = $easter - (2 * $day);
		$corpusChristi = $easter + (60 * $day);

		return array(
			'01/01/' . $year,
			'02/02/' . $year,
			date('d/m/Y', $carnaval),
			date('d/m/Y', $sextaSanta),
			date('d/m/Y', $easter),
			'21/04/' . $year,
			'01/05/' . $year,
			date('d/m/Y', $corpusChristi),
			'20/09/' . $year,
			'12/10/' . $year,
			'02/11/' . $year,
			'15/11/' . $year,
			'25/12/' . $year,
		);
	}
}
