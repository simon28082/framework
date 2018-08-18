<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-07-09 06:47
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Http\Middleware\Passport;

use Closure;
use CrCms\Foundation\Sso\Client\Contracts\InteractionContract;
use Illuminate\Http\Request;
use Exception;

/**
 * Class AuthMiddleware
 * @package CrCms\Foundation\App\Http\Middleware\Passport
 */
class AuthMiddleware
{
    /**
     * @var InteractionContract
     */
    protected $passport;

    /**
     * CheckMiddleware constructor.
     * @param InteractionContract $passport
     */
    public function __construct(InteractionContract $passport)
    {
        $this->passport = $passport;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->input('token', str_replace('Bearer ', $request->header('Authorization')));

        try {
            $result = $this->passport->check($token);
            dd($result);
        } catch (Exception $exception) {
            dd($exception);
            throw new UnauthorizedHttpException($exception);
        }

        return $next($request);
    }
}