<?php

namespace CrCms\Foundation\Swoole\Server\Contracts;

/**
 * Interface TaskServerContract
 * @package CrCms\Foundation\App\Tasks\Contracts
 */
interface TaskContract
{
    /**
     * @param mixed ...$params
     * @return mixed
     */
    public function handle(...$params);

    /**
     * @param $data
     * @return mixed
     */
    public function finish($data);
}