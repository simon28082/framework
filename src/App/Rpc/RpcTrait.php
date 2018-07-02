<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/3 6:40
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Rpc;

use CrCms\Foundation\Rpc\Contracts\RpcContract;

class RpcTrait
{
    /**
     * @var RpcContract
     */
    protected $rpc;

    /**
     * @return RpcContract
     */
    public function instance(): RpcContract
    {
        return app(RpcContract::class);
    }
}