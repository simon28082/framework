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

/**
 * This recursive array merge function doesn't renumber integer keys and appends new values to existing ones
 * OR adds a new [key => value] pair if the pair doesn't exist.
 *
 * @param array $array1
 * @param array $array2
 * @return array
 */
function array_merge_recursive_adv(array &$array1, array $array2) {
    foreach ($array2 as $key => $value) {
        if (array_key_exists($key, $array1)) {
            if (is_array($value)) {
                array_merge_recursive_adv($array1[$key], $value);
            } else {
                if (!empty($array1[$key])) {
                    if (is_array($array1[$key])) {
                        array_push($array1[$key], $value);
                    } else {
                        $array1[$key] = [$array1[$key]];
                        $array1[$key][] = $value;
                    }
                } else if (empty($array1[$key])) {
                    $array1[$key] = $value;
                }
            }
        } else {
            $array1[$key] = $value;
        }
    }
    return $array1;
}

function framework_path($path = '')
{
    return app('path.framework').($path ? DIRECTORY_SEPARATOR.$path : $path);
}

function framework_config_path($path = '')
{
    return app('path.framework_config').($path ? DIRECTORY_SEPARATOR.$path : $path);
}

function framework_resource_path($path = '')
{
    return app('path.framework_resource').($path ? DIRECTORY_SEPARATOR.$path : $path);
}