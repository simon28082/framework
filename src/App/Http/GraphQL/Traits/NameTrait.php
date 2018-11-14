<?php

namespace CrCms\Framework\App\Http\GraphQL\Traits;

/**
 * Trait NameTrait
 * @package CrCms\Framework\App\Http\GraphQL\Traits
 */
trait NameTrait
{
    /**
     * @return self
     */
    protected function setAttributeName(): self
    {
        $this->attributes['name'] = $this->setCurrentName();
        return $this;
    }

    /**
     * @return string
     */
    protected function setCurrentName(): string
    {
        return $this->getNameByClass(get_class($this));
    }

    /**
     * @return string
     */
    public function getCurrentName(): string
    {
        return $this->attributes['name'];
    }

    /**
     * @param string $className
     * @return string
     */
    public function getNameByClass(string $className): string
    {
        return 'x' . md5($className);
    }
}