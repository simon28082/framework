<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:41
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Http;

use CrCms\Foundation\Rpc\Contracts\ResponseContract;
use CrCms\Foundation\Rpc\Contracts\HttpRequestContract;
use CrCms\Foundation\Rpc\Contracts\RequestContract;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use InvalidArgumentException;

/**
 * Class Response
 * @package CrCms\Foundation\Rpc\Http
 */
class Response implements ResponseContract
{
    /**
     * @var object
     */
    protected $data;

    /**
     * @param RequestContract $request
     * @return ResponseContract
     */
    public function parse(RequestContract $request): ResponseContract
    {
        $this->throwIfError($request);

        $this->resovleData($request);

        return $this;
    }

    /**
     * @param HttpRequestContract $request
     */
    protected function throwIfError(HttpRequestContract $request)
    {
        $statusCode = $request->getStatusCode();

        if ($statusCode >= 300) {
            throw new RuntimeException("Request is redirected, status code:{$statusCode}");
        }
    }

    /**
     * @param HttpRequestContract $request
     */
    protected function resovleData(HttpRequestContract $request)
    {
        $data = $request->getContent();

        if (is_array($data)) {
            $this->data = (object)$data;
        } else if ((bool)($newData = json_decode($data)) && json_last_error() === 0) {
            $this->data = $newData;
        } else {
            $this->data = $data;
        }
    }

    /**
     * @return object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (isset($this->data->{$name})) {
            return $this->data->{$name};
        }

        throw new InvalidArgumentException("The attribute[{$name}] is not exists");
    }
}