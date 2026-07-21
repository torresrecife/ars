<?php

declare(strict_types=1);

namespace App\Support;

class MonthMap
{
    public static function localized()
    {
        return array(
            1 => __('January'),
            2 => __('February'),
            3 => __('March'),
            4 => __('April'),
            5 => __('May'),
            6 => __('June'),
            7 => __('July'),
            8 => __('August'),
            9 => __('September'),
            10 => __('October'),
            11 => __('November'),
            12 => __('December'),
        );
    }
}
