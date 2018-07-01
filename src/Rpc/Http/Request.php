<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:20
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Http;

use CrCms\Foundation\Client\Client;
use CrCms\Foundation\Rpc\Contracts\Request as RequestContract;
use CrCms\Foundation\Rpc\Contracts\Response as ResponseContract;

class Request implements RequestContract
{

    protected $headers = [
        'User-Agent'=> 'JSON-RPC PHP Client <https://github.com/fguillot/JsonRPC>',
        'Content-Type'=> 'application/json',
        'Accept'=>' application/json',
        'Connection'=>' close',
    ];

    protected $path;

    protected $data;

    protected $client;

    protected $response;

    public function __construct(Client $client)
    {
        $this->client = $client;
//        $this->response = $response;
    }


    public function sendPayload(string $name, array $params = []): ResponseContract
    {
        try {
            $connection = $this->client->connection('http')->setHeaders($this->headers)
                ->setMethod('post')
                ->send($name, ['payload'=>$params]);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }


        //$this->response->parse();
        return new Response($connection->getStatusCode(),$connection->getContent());
    }


}