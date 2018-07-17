<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-07-17 21:25
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\App\Handlers\Traits;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

/**
 * Trait HttpHandlerTrait
 * @package CrCms\Foundation\App\Handlers
 */
trait HttpHandlerTrait
{
    use ValidatesRequests;

    /**
     * @var Request
     */
    protected $request;
}