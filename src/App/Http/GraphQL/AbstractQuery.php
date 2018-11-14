<?php

namespace CrCms\Framework\App\Http\GraqhQL;

use CrCms\Framework\App\Http\GraphQL\Traits\NameTrait;
use CrCms\Framework\App\Http\GraphQL\Traits\ShouldValidateExtendTrait;
use Folklore\GraphQL\Support\Query;
use Folklore\GraphQL\Support\Traits\ShouldValidate;

/**
 * Class CategoryQuery
 * @package CrCms\Category\Http\GraphQL\Queries
 */
abstract class AbstractQuery extends Query
{
    use NameTrait, ShouldValidate, ShouldValidateExtendTrait;

    public function __construct()
    {
        parent::__construct([]);
        $this->setAttributeName();
    }
}