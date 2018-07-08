<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-07-09 06:47
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

/**
 * Class ApiResponseType
 * @package CrCms\Foundation\App\Http\Middleware
 */
class ApiResponseType
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!$response->headers->has('X-CRCMS-Media-Type')) {
            $response->header('X-CRCMS-Media-Type: ', config('foundation.api_type'));
        }

        if (!$response->headers->has('X-CRCMS-Media-Version')) {
            $response->header('X-CRCMS-Media-Version: ', config('foundation.api_version'));
        }

        return $response;
    }
}