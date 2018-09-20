<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/07/04 11:00
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Http\Guzzle;

use CrCms\Foundation\ConnectionPool\AbstractConnector;
use CrCms\Foundation\ConnectionPool\Contracts\Connector as ConnectorContract;
use GuzzleHttp\Client;

/**
 * Class GuzzleHttpConnector
 * @package CrCms\Foundation\ConnectionPool\Connectors
 */
class Connector extends AbstractConnector implements ConnectorContract
{
    /**
     * @param array $config
     * @return Connector
     */
    public function connect(array $config): ConnectorContract
    {
        $settings = $this->mergeSettings($config['settings'] ?? []);
        $settings['base_uri'] = $this->baseUri($this->scheme($settings), $config);
        $this->connect = new Client($settings);

        return $this;
    }

    public function close(): void
    {
    }

    /**
     * @param array $settings
     * @return string
     */
    protected function scheme(array $settings): string
    {
        return isset($settings['ssl']) && $settings['ssl'] ? 'https' : 'http';
    }

    /**
     * @param string $scheme
     * @param array $config
     * @return string
     */
    protected function baseUri(string $scheme, array $config): string
    {
        return $scheme . '://' . $config['host'] . ':' . $config['port'];
    }
}