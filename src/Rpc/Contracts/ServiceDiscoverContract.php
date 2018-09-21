<?php

namespace CrCms\Foundation\Rpc\Contracts;

/**
 * Class ServiceDiscoverContract
 * @package CrCms\Foundation\Rpc\Contracts
 */
interface ServiceDiscoverContract
{
    /**
     * @param string $service
     * @return array
     */
    public function discover(string $service): array;
}