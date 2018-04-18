<?php

namespace CrCms\Foundation\App\Http\GraphQL;

use CrCms\Foundation\App\Http\GraphQL\Traits\FieldTrait;
use CrCms\Foundation\App\Http\GraphQL\Traits\NameTrait;
use Folklore\GraphQL\Support\Type as GraphQLType;

/**
 * Class AbstractType
 * @package CrCms\Foundation\App\Http\GraphQL
 */
abstract class AbstractType extends GraphQLType
{
    use NameTrait, FieldTrait;

    public function __construct()
    {
        parent::__construct([]);
        $this->setAttributeName();
    }
}