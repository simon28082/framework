<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:41
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Rpc\Http;

use function CrCms\Foundation\App\Helpers\is_serialized;
use CrCms\Foundation\Client\Contracts\Connection;
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
     * @var Connection
     */
    protected $connection;

    /**
     * @param RequestContract $request
     * @return ResponseContract
     */
    public function parse(Connection $connection): ResponseContract
    {
        $this->connection = $connection;

        $this->throwIfError();

        $this->resovleData();

        return $this;
    }

    /**
     *
     */
    protected function throwIfError()
    {
        $statusCode = $this->getStatusCode();

        if ($statusCode >= 300) {
            throw new RuntimeException("Request is redirected, status code:{$statusCode}");
        }
    }

    /**
     *
     */
    protected function resovleData()
    {
        $data = $this->getContent();

        if (is_array($data)) {
            $this->data = (object)$data;
        } else if ((bool)($newData = json_decode($data)) && json_last_error() === 0) {
            $this->data = $newData;
        } else if (is_serialized($data)) {
            $this->data = unserialize($data);
        } else {
            $this->data = $data;
        }
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->connection->getStatusCode();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->connection->getContent();
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