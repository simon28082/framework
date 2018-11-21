<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/16 17:41
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Framework\Http;

use CrCms\Framework\Http\Events\MessageEvent;
use CrCms\Framework\Http\Events\RequestEvent;
use CrCms\Server\Server\AbstractServer;
use CrCms\Server\Server\Contracts\ServerContract;
use Illuminate\Contracts\Http\Kernel;
use Swoole\WebSocket\Server as WebSocketServer;

/**
 * Class Server
 * @package CrCms\Framework\Swoole\Http
 */
class Server extends AbstractServer implements ServerContract
{
    /**
     * @var array
     */
    protected $events = [
        'message' => MessageEvent::class,
        'request' => RequestEvent::class,
    ];

    /**
     * @return void
     */
    public function bootstrap(): void
    {
        $this->app->make(Kernel::class)->bootstrap();
    }

    /**
     * @param array $config
     * @return SwooleServer
     */
    public function createServer(): void
    {
        $serverParams = [
            $this->config['host'],
            $this->config['port'],
            $this->config['mode'] ?? SWOOLE_PROCESS,
            $this->config['type'] ?? SWOOLE_SOCK_TCP,
        ];

        $this->server = new WebSocketServer(...$serverParams);
        $this->setPidFile();
        $this->setSettings($this->config['settings'] ?? []);
        $this->eventDispatcher($this->config['events'] ?? []);
    }

    /**
     * @return void
     */
    protected function setPidFile()
    {
        if (empty($this->config['settings']['pid_file'])) {
            $this->config['settings']['pid_file'] = $this->pidFile();
        }
    }
}