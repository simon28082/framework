<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-15 07:17
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Sso\Client;

use CrCms\Foundation\Rpc\Contracts\RpcContract;
use CrCms\Foundation\Sso\Client\Contracts\InteractionContract;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\ClientException;
use Exception;

/**
 * Class DefaultInteractor
 * @package CrCms\Foundation\Sso\Client
 */
class DefaultInteractor implements InteractionContract
{
    /**
     * @var RpcContract
     */
    protected $rpc;

    /**
     * DefaultInteractor constructor.
     * @param RpcContract $rpc
     */
    public function __construct(RpcContract $rpc)
    {
        $this->rpc = $rpc;
    }

    /**
     * @param string $token
     * @return array
     */
    public function refresh(string $token): array
    {
        $response = $this->rpc->call('passport.api.v1.refresh-token', $this->requestParams(['token' => $token]));
        return (array)$response->getData();
    }

    /**
     * @param string $token
     * @return array
     */
    public function user(string $token): array
    {
        $response = $this->rpc->call('passport.api.v1.refresh-token', $this->requestParams(['token' => $token]));
        return (array)$response->getData();
    }

    /**
     * @param string $token
     * @return bool
     */
    public function check(string $token): bool
    {
        $response = $this->rpc->call('passport.api.v1.check-login', $this->requestParams(['token' => $token]));
        return (array)$response->getData();
    }

    /**
     * @param array $params
     * @return array
     */
    protected function requestParams(array $params): array
    {
        return array_merge(['app_key' => config('foundation.passport_key'), 'app_secret' => config('foundation.passport_secret')], $params);
    }
}