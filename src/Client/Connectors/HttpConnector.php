<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 20:42
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Connectors;

use CrCms\Foundation\Client\AbstractConnector;
use CrCms\Foundation\Client\Contracts\Connector;
use Swoole\Coroutine\Http\Client;

/**
 * Class HttpConnector
 * @package CrCms\Foundation\Client\Connectors
 */
class HttpConnector extends AbstractConnector implements Connector
{
    /**
     * @var array
     */
    protected $defaultHeaders = [
        'Content-Type' => 'application/json',
    ];

    /**
     * @param array $config
     * @return Connector
     */
    public function connect(array $config): Connector
    {
        $this->connect = new Client($config['host'], $config['port']);
        $this->connect->set($this->mergeSettings($config['settings'] ?? []));
        $this->connect->setHeaders($this->mergeHeaders([]));
        return $this;
    }

    /**
     * @param $headers
     * @return array
     */
    protected function mergeHeaders($headers): array
    {
        return array_merge($this->defaultHeaders, $headers);
    }
}