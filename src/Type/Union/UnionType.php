<?php

namespace Youshido\GraphQL\Type\Union;

use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class UnionType
 */
final class UnionType extends AbstractUnionType
{
    /**
     * @param object               $object
     * @param ResolveInfoInterface $resolveInfo
     *
     * @return AbstractObjectType
     */
    public function resolveType($object, ResolveInfoInterface $resolveInfo)
    {
        $callable = $this->getConfigValue('resolveType');

        return $callable($object);
    }

    /**
     * @return callable|mixed|null
     */
    public function getTypes()
    {
        return $this->getConfig()->get('types');
    }
}
