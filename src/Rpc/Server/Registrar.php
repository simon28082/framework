<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/30 5:52
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Server;


interface Registrar
{

    /**
     * Register 
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return
     */
    public function register(string $uri, $action);

}