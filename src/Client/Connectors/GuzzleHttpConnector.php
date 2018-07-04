<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/07/04 11:00
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Connectors;

use CrCms\Foundation\Client\AbstractConnector;
use CrCms\Foundation\Client\Contracts\Connector;
use GuzzleHttp\Client;

/**
 * Class GuzzleHttpConnector
 * @package CrCms\Foundation\Client\Connectors
 */
class GuzzleHttpConnector extends AbstractConnector implements Connector
{
    /**
     * @param array $config
     * @return Connector
     */
    public function connect(array $config): Connector
    {
        $settings = $this->mergeSettings($config['settings'] ?? []);
        $settings['base_uri'] = $this->baseUri($this->scheme($settings), $config);
        $this->connect = new Client($settings);

        return $this;
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