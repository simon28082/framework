<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-06 19:18
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Handlers;

use CrCms\Foundation\App\Handlers\Contracts\JWTPassportContract;
use CrCms\Foundation\App\Handlers\Traits\RepositoryHandlerTrait;
use CrCms\Foundation\App\Handlers\Traits\RequestHandlerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class JWTPassportHandler
 * @package CrCms\Foundation\App\Handlers
 */
class JWTPassportHandler extends AbstractHandler
{
    use RequestHandlerTrait, RepositoryHandlerTrait;

    /**
     * JWTPassportHandler constructor.
     * @param Request $request
     * @param JWTPassportContract $repository
     */
    public function __construct(Request $request, JWTPassportContract $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function handle(): array
    {
        $this->validate($this->request, [
            'ticket' => ['required'],
        ]);

        $user = $this->repository->getUser($this->request->input('ticket'));

        if ((bool)($diff = now()->diffInSeconds($user->ticket_expired_at, false)) < 0) {
            throw new UnauthorizedHttpException('error');
        }

        return [
            //fromUser 生成的token认证无效，真是奇怪
            'token' => Auth::guard()->setTTL($diff)->fromUser($user),
//            'token' => Auth::guard()->setTTL($diff)->tokenById($user->id),
            'expire' => $diff,
        ];
    }
}