<?php

namespace CrCms\Foundation\MicroService\Http;

use CrCms\Foundation\MicroService\Contracts\ResponseContract;
use Illuminate\Http\JsonResponse;
//use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * Class Response
 * @package CrCms\Foundation\MicroService\Http
 */
class Response extends JsonResponse implements ResponseContract
{

//    public function send(): void
//    {
//    }
}