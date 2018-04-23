<?php

namespace CrCms\Foundation\App\Helpers;

use Carbon\Carbon;

/**
 * @param array|string $dates
 * @return array|string
 */
function date_to_timestamp($dates)
{
    if (!is_array($dates)) {
        return Carbon::parse($dates)->getTimestamp();
    }

    return array_map(function ($date) {
        return Carbon::parse($date)->getTimestamp();
    }, $dates);
}

/**
 * @param string $sub
 * @return string
 */
function sub_domain(string $sub): string
{
    return "{$sub}." . config('app.domain');
}