<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-07-17 20:40
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Handlers;

use Illuminate\Contracts\Container\Container;

/**
 * Class AbstractHandler
 * @package CrCms\Foundation\App\Actions
 */
abstract class AbstractHandler implements HandlerContract
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * AbstractHandler constructor.
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }
}