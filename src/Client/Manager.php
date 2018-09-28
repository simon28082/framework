<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client;

use CrCms\Foundation\Application;
use CrCms\Foundation\ConnectionPool\ConnectionManager;
use CrCms\Foundation\ConnectionPool\Contracts\ConnectionFactory;
use InvalidArgumentException;
use BadMethodCallException;
use Crcms\Foundation\ConnectionPool\Contracts\Connection;

/**
 * @method string id()
 * @method bool isRelease()
 * @method void makeRelease()
 * @method void makeRecycling()
 * @method bool isAlive()
 * @method void makeAlive()
 * @method void markDead()
 * @method void reconnection()
 * @method Manager request(string $uri, array $data = []);
 * @method mixed getResponse()
 * @method mixed getContent()
 * @method int getLaseActivityTime()
 * @method int getConnectionNumber()
 *
 * Class Manager
 * @package CrCms\Foundation\Client
 */
class Manager
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ConnectionManager
     */
    protected $connectionPoolManager;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var ConnectionFactory
     */
    protected $factory;

    /**
     * @var bool
     */
    protected $usePool;

    /**
     * Manager constructor.
     * @param Application $app
     */
    public function __construct(Application $app, ConnectionFactory $factory, ?ConnectionManager $poolManager = null)
    {
        $this->app = $app;
        $this->factory = $factory;
        $this->connectionPoolManager = $poolManager;
    }

    /**
     * @param null $name
     * @param bool $usePool
     * @return $this
     */
    public function connection($name = null, $usePool = true)
    {
        if (is_array($name)) {
            list($name, $config) = [$name['name'] ?? '', $name];
        } else {
            $name = $name ? $name : $this->defaultDriver();
            $config = $this->configuration($name);
        }

        $this->usePool = $usePool;

        $this->connection = $this->usePool ? $this->connectionPoolManager->connection(
            $this->factory->config($config)
            , $name
        ) : $this->factory->config($config)->make();

        return $this;
    }

    /**
     * @return ConnectionFactory
     */
    public function getFactory(): ConnectionFactory
    {
        return $this->factory;
    }

    /**
     * @return void
     */
    public function close(): void
    {
        if ($this->connectionPoolManager && $this->usePool) {
            $this->connectionPoolManager->close($this->connection);
        }

        $this->connection = null;
    }

    /**
     * @return ConnectionManager
     */
    public function getConnectionPoolManager(): ConnectionManager
    {
        return $this->connectionPoolManager;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    /*protected function poolName(): string
    {
        return $this->app->make('config')->get('client.pool');
    }*/

    /**
     * @return string
     */
    protected function defaultDriver(): string
    {
        return $this->app->make('config')->get('client.default');
    }

    /**
     * @param string $name
     * @return array
     */
    protected function configuration(string $name): array
    {
        $connections = $this->app->make('config')->get('client.connections');

        if (!isset($connections[$name])) {
            throw new InvalidArgumentException("Client config[{$name}] not found");
        }

        return $connections[$name];
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        //接手控制权
        if ($this->connection instanceof Connection) {
            $result = call_user_func_array([$this->connection, $name], $arguments);
            if ($result instanceof Connection) {
                $this->connection = $result;
                return $this;
            }

            return $result;
        }

        throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}