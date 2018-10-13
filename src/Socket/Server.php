<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/17 16:23
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Socket;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\Server\Contracts\ServerContract;
use CrCms\Foundation\Socket\Events\ReceiveEvent;
use Illuminate\Contracts\Http\Kernel;
use Swoole\Server as SocketServer;

/**
 * Class Server
 * @package CrCms\Foundation\Socket
 */
class Server extends AbstractServer implements ServerContract
{
    /**
     * @var array
     */
    protected $events = [
        'receive' => ReceiveEvent::class
    ];

    /**
     *
     */
    protected function bootstrap(): void
    {
        $this->app->make(Kernel::class)->bootstrap();
    }

    /**
     *
     */
    public function createServer(): void
    {
        $serverParams = [
            $this->config['host'],
            $this->config['port'],
            $this->config['mode'] ?? SWOOLE_PROCESS,
            $this->config['type'] ?? SWOOLE_SOCK_TCP,
        ];

        $this->server = new SocketServer(...$serverParams);
        $this->setSettings($this->config['settings'] ?? []);
        $this->eventDispatcher($this->config['events'] ?? []);
    }
}