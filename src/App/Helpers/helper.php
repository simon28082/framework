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
//function array_merge_recursive_adv(array &$array1, array $array2) {
//    foreach ($array2 as $key => $value) {
//        if (array_key_exists($key, $array1)) {
//            if (is_array($value)) {
//                array_merge_recursive_adv($array1[$key], $value);
//            } else {
//                if (!empty($array1[$key])) {
//                    if (is_array($array1[$key])) {
//                        array_push($array1[$key], $value);
//                    } else {
//                        $array1[$key] = [$array1[$key]];
//                        $array1[$key][] = $value;
//                    }
//                } else if (empty($array1[$key])) {
//                    $array1[$key] = $value;
//                }
//            }
//        } else {
//            $array1[$key] = $value;
//        }
//    }
//    return $array1;
//}

/**
 * @param array
 * @param array
 * @return array
 */
function array_merge_recursive_distinct(): array
{
    $arrays = func_get_args();
    $base = array_shift($arrays);
    if (!is_array($base)) $base = empty($base) ? array() : array($base);
    foreach ($arrays as $append) {
        if (!is_array($append)) $append = array($append);
        foreach ($append as $key => $value) {
            if (!array_key_exists($key, $base) and !is_numeric($key)) {
                $base[$key] = $append[$key];
                continue;
            }
            if (is_array($value) or is_array($base[$key])) {
                $base[$key] = array_merge_recursive_distinct($base[$key], $append[$key]);
            } else if (is_numeric($key)) {
                if (!in_array($value, $base)) $base[] = $value;
            } else {
                $base[$key] = $value;
            }
        }
    }
    return $base;
}

/**
 * @param array
 * @param array
 * @return array
 */
function array_merge_recursive_adv(): array
{
    if (func_num_args() < 2) {
        trigger_error(__FUNCTION__ . ' needs two or more array arguments', E_USER_WARNING);
        return [];
    }
    $arrays = func_get_args();
    $merged = array();
    while ($arrays) {
        $array = array_shift($arrays);
        if (!is_array($array)) {
            trigger_error(__FUNCTION__ . ' encountered a non array argument', E_USER_WARNING);
            return [];
        }
        if (!$array)
            continue;
        foreach ($array as $key => $value)
            if (is_string($key))
                if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key]))
                    $merged[$key] = call_user_func(__FUNCTION__, $merged[$key], $value);
                else
                    $merged[$key] = $value;
            else
                $merged[] = $value;
    }
    return $merged;
}

function framework_path($path = '')
{
    return app('path.framework') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

function framework_config_path($path = '')
{
    return app('path.framework_config') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

function framework_resource_path($path = '')
{
    return app('path.framework_resource') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}


function is_serialized($data): bool
{
    $data = trim($data);
    if ('N;' == $data)
        return true;
    if (!preg_match('/^([adObis]):/', $data, $badions))
        return false;
    switch ($badions[1]) {
        case 'a' :
        case 'O' :
        case 's' :
            if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
                return true;
            break;
        case 'b' :
        case 'i' :
        case 'd' :
            if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data))
                return true;
            break;
    }
    return false;
}