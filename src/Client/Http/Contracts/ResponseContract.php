<?php

namespace CrCms\Foundation\Client\Http\Contracts;

/**
 * Interface ResponseContract
 * @package CrCms\Foundation\Client\Contracts
 */
interface ResponseContract
{
    /**
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * @return mixed
     */
    public function getContent();
}