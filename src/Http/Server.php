<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/16 17:41
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Http;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Http\Events\RequestEvent;
use CrCms\Foundation\Swoole\Server\Contracts\ServerContract;
use Illuminate\Contracts\Http\Kernel;
use Swoole\Http\Server as HttpServer;

/**
 * Class Server
 * @package CrCms\Foundation\Swoole\Http
 */
class Server extends AbstractServer implements ServerContract
{
    /**
     * @var array
     */
    protected $events = [
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

        $this->server = new HttpServer(...$serverParams);
        $this->setSettings($this->config['settings'] ?? []);
        $this->eventDispatcher($this->config['events'] ?? []);
    }


}