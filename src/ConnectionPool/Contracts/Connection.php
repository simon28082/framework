<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:33
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\ConnectionPool\Contracts;

/**
 * Interface Connection
 * @package CrCms\Foundation\ConnectionPool\Contracts
 */
interface Connection
{
    /**
     * @return string
     */
    public function id(): string;

    /**
     * @return bool
     */
    public function isRelease(): bool;

    /**
     * @return void
     */
    public function release(): void;

    /**
     * @return bool
     */
    public function isAlive(): bool;

    /**
     * @return void
     */
    public function makeAlive(): void;

    /**
     * @return void
     */
    public function markDead(): void;

    /**
     * @return void
     */
    public function reconnection(): void;

    /**
     * @param string $uri
     * @param array $data
     * @return Connection
     */
    public function request(string $uri, array $data = []): Connection;

    /**
     * @return mixed
     */
    public function getResponse();

    /**
     * @return int
     */
    public function getLaseActivityTime(): int;

    /**
     * @return int
     */
    public function getConnectionNumber(): int;
}