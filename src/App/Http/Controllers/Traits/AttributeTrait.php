<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-04-03 21:51
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Framework\App\Http\Controllers\Traits;

use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Trait AttributeTrait
 * @package CrCms\Framework\App\Http\Controllers\Traits
 */
trait AttributeTrait
{
    /**
     * @var string
     */
    protected $attributeClassName;

    /**
     * @param null|string $type
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function getAttributes(?string $type = null)
    {
        if (empty($type)) {
            $attributes = call_user_func([$this->attributeClassName,'getStaticAttributes']);
        } else {
            $attributes = call_user_func([$this->attributeClassName,'getStaticTransform'],$type);
            $attributes = [$type => $attributes];
            if (empty($attributes)) {
                throw new NotFoundResourceException("The type[{$type}] not found");
            }
        }

        return $this->response->array(['data' => $attributes]);
    }
}