<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/26 6:25
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Contracts;


interface Connector
{

    public function connect(array $config);

}