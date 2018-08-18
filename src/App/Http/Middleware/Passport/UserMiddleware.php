<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-18 22:03
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Http\Middleware\Passport;

use CrCms\App\User;
use CrCms\Foundation\App\Helpers\InstanceTrait;
use CrCms\Foundation\Sso\Client\Contracts\InteractionContract;
use Illuminate\Http\Request;
use Closure;

/**
 * Class UserMiddleware
 * @package CrCms\Foundation\App\Http\Middleware\Passport
 */
class UserMiddleware extends AbstractPassportMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     */
    public function handle(Request $request, Closure $next)
    {
        $this->guard()->setUser(
            new User($this->passport->user($this->token($request)))
        );

        return $next($request);
    }
}