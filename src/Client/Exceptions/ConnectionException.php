<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/07/03 08:56
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\Client\Exceptions;

use CrCms\Foundation\Client\Contracts\Connection;
use RuntimeException;
use Throwable;

/**
 * Class ConnectionException
 * @package CrCms\Foundation\Client\Exceptions
 */
class ConnectionException extends RuntimeException
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * ConnectionException constructor.
     * @param Connection $connection
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(Connection $connection, string $message = "Connection failed", int $code = 0, Throwable $previous = null)
    {
        $this->connection = $connection;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }
}