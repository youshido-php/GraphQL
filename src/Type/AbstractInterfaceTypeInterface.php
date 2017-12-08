<?php

namespace Youshido\GraphQL\Type;

/**
 * Interface AbstractInterfaceTypeInterface
 */
interface AbstractInterfaceTypeInterface
{
    /**
     * @param $object object from resolve function
     *
     * @return AbstractType
     */
    public function resolveType($object);
}
