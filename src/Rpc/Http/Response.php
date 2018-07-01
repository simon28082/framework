<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:41
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Http;

use CrCms\Foundation\Rpc\Contracts\Response as ResponseContract;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use InvalidArgumentException;

class Response implements ResponseContract
{
    public function parse(): ResponseContract
    {

        if ($this->statusCode >= 400) {
            throw new HttpException($this->statusCode,$this->data->message);
        } elseif ($this->statusCode >= 300) {
            throw new RuntimeException("Request is redirected, status code:{$this->statusCode}");
        }

    }

    protected $data;

    protected $statusCode;

    public function __construct(int $statusCode, $data)
    {
        $this->statusCode = $statusCode;
        $this->data = is_array($data) ? $data :json_decode($data);
    }


    public function __get(string $name)
    {
        if (isset($this->data->{$name})) {
            return $this->data->{$name};
        }

        throw new InvalidArgumentException("The attribute[{$name}] is not exists");
    }
}