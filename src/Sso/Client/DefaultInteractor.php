<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-15 07:17
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Sso\Client;

use CrCms\Foundation\Sso\Client\Contracts\InteractionContract;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\ClientException;

/**
 * Class DefaultInteractor
 * @package CrCms\Foundation\Sso\Client
 */
class DefaultInteractor implements InteractionContract
{
    protected $request;

    protected $client;

    protected $headers = ['Content-Type' => 'application/json', 'Accept' => 'application/json'];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function refresh(): array
    {
        // TODO: Implement refresh() method.
    }

    public function token(): array
    {
        // TODO: Implement token() method.
    }

    public function user(): array
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