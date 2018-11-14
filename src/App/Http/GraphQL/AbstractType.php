<?php

namespace CrCms\Framework\App\Http\GraphQL;

use CrCms\Framework\App\Http\GraphQL\Traits\FieldTrait;
use CrCms\Framework\App\Http\GraphQL\Traits\NameTrait;
use Folklore\GraphQL\Support\Type as GraphQLType;

/**
 * Class AbstractType
 * @package CrCms\Framework\App\Http\GraphQL
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