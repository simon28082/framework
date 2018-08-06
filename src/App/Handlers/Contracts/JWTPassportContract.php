<?php

/**
 * @author simon <simon@crcms.cn>
 * @datetime 2018-08-06 19:21
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Handlers\Contracts;

use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Interface JWTPassportContract
 * @package CrCms\Foundation\App\Handlers\Contracts
 */
interface JWTPassportContract
{
    /**
     * @param string $ticket
     * @return JWTSubject
     */
    public function getUser(string $ticket): JWTSubject;
}