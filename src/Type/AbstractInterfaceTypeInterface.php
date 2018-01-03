<?php

namespace Youshido\GraphQL\Type;

use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Interface AbstractInterfaceTypeInterface
 *
 * todo: we need this interface?
 */
interface AbstractInterfaceTypeInterface
{
    /**
     * @param object               $object from resolve function
     * @param ResolveInfoInterface $resolveInfo
     *
     * @return AbstractObjectType
     */
    public function resolveType($object, ResolveInfoInterface $resolveInfo);
}
