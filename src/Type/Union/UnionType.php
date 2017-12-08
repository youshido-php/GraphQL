<?php

namespace Youshido\GraphQL\Type\Union;

/**
 * Class UnionType
 */
final class UnionType extends AbstractUnionType
{
    protected $isFinal = true;

    /**
     * @param object $object
     *
     * @return mixed
     */
    public function resolveType($object)
    {
        $callable = $this->getConfigValue('resolveType');

        return $callable($object);
    }

    /**
     * @return callable|mixed|null
     */
    public function getTypes()
    {
        return $this->getConfig()->get('types', []);
    }
}
