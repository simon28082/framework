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

abstract class AbstractConnection implements ConnectionContract
{
    protected $isAlive;

    protected $connector;

    protected $config;

    public function __construct(Connector $connector, array $config = [])
    {
        $this->connector = $connector;
        $this->config = $config;
    }

    public function isAlive(): bool
    {
        return $this->isAlive;
    }

    public function makeAlive(): ConnectionContract
    {
        $this->isAlive = true;
        return $this;
    }

    public function markDead(): ConnectionContract
    {
        $this->isAlive = false;
        return $this;
    }

}