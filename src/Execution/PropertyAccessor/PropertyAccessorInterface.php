<?php

namespace Youshido\GraphQL\Execution\PropertyAccessor;

/**
 * Interface PropertyAccessorInterface
 */
interface PropertyAccessorInterface
{
    /**
     * @param mixed  $object
     * @param string $property
     *
     * @return mixed
     */
    public function getValue($object, $property);
}
