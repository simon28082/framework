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

    public function __construct(RpcContract $rpc)
    {
        $this->rpc = $rpc;
    }

    public function refresh(string $token): array
    {
        try {
            $response = $this->rpc->call('passport.api.v1.refresh-token', ['token' => $token,'app_key'=>'2222222222','app_secret'=>'']);
            dd($response);
            return (array)$response->getData();
        } catch (Exception $exception) {
            dd($exception->getMessage());
            throw $exception;
        }
    }

    public function token(string $token): array
    {
        // TODO: Implement token() method.
    }

    public function user(string $token): array
    {
        // TODO: Implement user() method.
    }

    public function check(string $token): bool
    {
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://passport.crcms.local/api/v1/', 'timeout' => 1]);
        try {
            $response = $this->client->get('check-login', [
                'headers' => $this->headers,
                'query' => $this->requestParams(['token' => $token])
            ]);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $exception) {
            $statusCode = ($exception->getResponse()->getStatusCode());
        }

        return $statusCode === 403;
    }

    /**
     * @param array $params
     * @return array
     */
    protected function requestParams(array $params): array
    {
        return array_merge(['app_key' => config('sso.client.app_key'), 'app_secret' => config('sso.client.app_secret')], $params);
    }
}