<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/17 7:18
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\WebSocket;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\Server\Contracts\ServerContract;
use CrCms\Foundation\Swoole\WebSocket\Events\MessageEvent;
use CrCms\Foundation\Swoole\WebSocket\Events\OpenEvent;
use Swoole\Server as SwooleServer;
use Swoole\WebSocket\Server as WebSocketServer;

/**
 * Class Server
 * @package CrCms\Foundation\Swoole\WebSocket
 */
class Server extends AbstractServer implements ServerContract
{
    /**
     * @var array
     */
    protected $events = [
        'open' => OpenEvent::class,
        'message' => MessageEvent::class,
    ];

    /**
     * @return void
     */
    protected function bootstrap(): void
    {
        // TODO: bind Kernel bootstrap
    }

    /**
     * @param array $config
     * @return void
     */
    public function createServer(array $config): void
    {
        $serverParams = [
            $config['host'],
            $config['port'],
            $config['mode'] ?? SWOOLE_PROCESS,
            $config['type'] ?? SWOOLE_SOCK_TCP,
        ];

        $this->server = new WebSocketServer(...$serverParams);
        $this->setSettings($config['settings'] ?? []);
        $this->eventDispatcher($config['events'] ?? []);
    }
}