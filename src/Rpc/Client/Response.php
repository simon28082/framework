<?php

/**
 * @author simon <434730525@qq.com>
 * @datetime 2018-06-28 15:17
 * @link http://www.koodpower.com/
 * @copyright Copyright &copy; 2018 Rights Reserved 快点动力
 */

namespace CrCms\Foundation\Rpc\Client;

use CrCms\Foundation\Rpc\Client\Contracts\Response as ResponseContract;

/**
 * Class Response
 * @package CrCms\Foundation\Rpc\Client
 */
class Response implements ResponseContract
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

}