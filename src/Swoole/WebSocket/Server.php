<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/17 7:18
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\WebSocket;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\WebSocket\Events\MessageEvent;
use CrCms\Foundation\Swoole\WebSocket\Events\OpenEvent;
use Swoole\Server as SwooleServer;
use Swoole\WebSocket\Server as WebSocketServer;

class Server extends AbstractServer
{
    protected $events = [
        'open' => OpenEvent::class,
        'message' => MessageEvent::class,
    ];

    protected function bootstrap(): void
    {
        // TODO: bind Kernel bootstrap
    }

    protected function createServer(array $config): SwooleServer
    {
        $serverParams = [
            $config['host'],
            $config['port'],
            $config['mode'] ?? SWOOLE_PROCESS,
            $config['type'] ?? SWOOLE_SOCK_TCP,
        ];

        return new WebSocketServer(...$serverParams);
    }
}