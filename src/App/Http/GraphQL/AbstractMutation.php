<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-04-01 12:04
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Framework\App\Http\GraqhQL;

use CrCms\Framework\App\Http\GraphQL\Traits\FieldTrait;
use CrCms\Framework\App\Http\GraphQL\Traits\InputTrait;
use CrCms\Framework\App\Http\GraphQL\Traits\NameTrait;
use CrCms\Framework\App\Http\GraphQL\Traits\ShouldValidateExtendTrait;
use Folklore\GraphQL\Support\Mutation;

/**
 * Class CategoryMutation
 * @package CrCms\Category\Http\GraphQL\Mutations
 */
abstract class AbstractMutation extends Mutation
{
    use NameTrait, FieldTrait, ShouldValidateExtendTrait;

    /**
     * AbstractMutation constructor.
     */
    public function __construct()
    {
        parent::__construct([]);
        $this->setAttributeName();
    }
}