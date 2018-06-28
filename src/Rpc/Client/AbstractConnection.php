<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:46
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Client;

use CrCms\Foundation\Rpc\Client\Contracts\Connection as ConnectionContract;
use Illuminate\Database\Connectors\Connector;

/**
 * Class AbstractConnection
 * @package CrCms\Foundation\Rpc\Client
 */
abstract class AbstractConnection implements ConnectionContract
{
    /**
     * @var bool
     */
    protected $isAlive = true;

    /**
     * @var Connector
     */
    protected $connector;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var
     */
    protected $connectionFailureTime;

    /**
     * @var int
     */
    protected $connectionFailureNum = 0;

    /**
     * AbstractConnection constructor.
     * @param Connector $connector
     * @param array $config
     */
    public function __construct(Connector $connector, array $config = [])
    {
        $this->connector = $connector;
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function isAlive(): bool
    {
        return $this->isAlive;
    }

    /**
     * @return ConnectionContract
     */
    public function makeAlive(): ConnectionContract
    {
        $this->isAlive = true;
        return $this;
    }

    /**
     * @return ConnectionContract
     */
    public function markDead(): ConnectionContract
    {
        $this->isAlive = false;
        return $this;
    }

    /**
     *
     */
    public function connectionFailure()
    {
        $this->connectionFailureNum += 1;
        $this->connectionFailureTime = time();
    }
}