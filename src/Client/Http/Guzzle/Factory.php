<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 6:13
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Http\Guzzle;

use CrCms\Foundation\ConnectionPool\AbstractConnectionFactory;
use CrCms\Foundation\ConnectionPool\Connectors\GuzzleHttpConnector;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionPool as ConnectionPoolContract;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionFactory as ConnectionFactoryContract;
use CrCms\Foundation\ConnectionPool\Contracts\Connection as ConnectionContract;

/**
 * Class ConnectionFactory
 * @package CrCms\Foundation\ConnectionPool
 */
class Factory extends AbstractConnectionFactory implements ConnectionFactoryContract
{
    /**
     * @param array $config
     * @return ConnectionContract
     */
    public function make(array $config, ConnectionPoolContract $pool): ConnectionContract
    {
        return $this->createConnection($config, $pool);
    }

    /**
     * @param array $config
     * @return ConnectionContract
     */
    protected function createConnection(array $config, ConnectionPoolContract $pool): ConnectionContract
    {
        return new Connection($pool, new GuzzleHttpConnector(), $config);
    }
}