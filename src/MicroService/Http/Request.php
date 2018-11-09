<?php

namespace CrCms\Foundation\MicroService\Http;

use CrCms\Foundation\MicroService\Contracts\RequestContract;
use Illuminate\Http\Request as BaseRequest;

/**
 * Class Request
 * @package CrCms\Foundation\MicroService\Http
 */
class Request extends BaseRequest implements RequestContract
{

}