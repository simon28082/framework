<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-04-01 13:31
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Framework\App\Http\GraphQL\Traits;

use Illuminate\Validation\Validator;

/**
 * Trait ShouldValidateExtendTrait
 * @package CrCms\Framework\App\Http\GraphQL\Traits
 */
trait ShouldValidateExtendTrait
{
    /**
     * @param Validator $validator
     * @param $args
     */
    protected function withValidator(Validator $validator, $args): void
    {
        $validator->setAttributeNames($this->ruleAttributes());
        return ;
    }

    /**
     * @return array
     */
    public function ruleAttributes(): array
    {
        return [
            'name' => trans('category::lang.category.name'),
            'sign' => trans('category::lang.category.sign'),
        ];
    }
}