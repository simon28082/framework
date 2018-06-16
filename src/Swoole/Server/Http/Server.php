<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/16 17:41
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Swoole\Server\Http;

use CrCms\Foundation\Swoole\Server\AbstractServer;
use CrCms\Foundation\Swoole\Server\Http\Events\RequestEvent;
use Illuminate\Contracts\Http\Kernel;
use Swoole\Http\Server as HttpServer;
use Swoole\Server as SwooleServer;

/**
 * Class Server
 * @package CrCms\Foundation\Swoole\Server\Http
 */
class Server extends AbstractServer
{
    protected $events = [
        'request' => RequestEvent::class,
    ];

    /**
     * @return void
     */
    protected function bootstrap(): void
    {
        $this->app->make(Kernel::class)->bootstrap();
    }

    /**
     * @param array $config
     * @return SwooleServer
     */
    protected function createServer(array $config): SwooleServer
    {
        $serverParams = [
            $config['host'],
            $config['port'],
            $config['mode'] ?? SWOOLE_PROCESS,
            $config['type'] ?? SWOOLE_SOCK_TCP,
        ];

        return new HttpServer(...$serverParams);
    }
}