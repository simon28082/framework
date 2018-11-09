<?php

namespace CrCms\Foundation\MicroService\Contracts;

/**
 * Class ServiceContract
 * @package CrCms\Foundation\MicroService\Contracts
 */
interface ServiceContract
{

    public function request();

    public function response($response);

    public function currentName() : string;
}