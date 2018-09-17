<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 6:13
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool;

use Illuminate\Contracts\Container\Container;

/**
 * Class AbstractConnectionFactory
 * @package CrCms\Foundation\ConnectionPool
 */
abstract class AbstractConnectionFactory
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * ConnectionFactory constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->app = $container;
    }
}