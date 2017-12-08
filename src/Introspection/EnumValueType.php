<?php

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

/**
 * Class EnumValueType
 */
class EnumValueType extends AbstractObjectType
{
    /**
     * @param \Youshido\GraphQL\Config\Object\ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField('name', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('deprecationReason', TypeMap::TYPE_STRING)
            ->addField('isDeprecated', new NonNullType(TypeMap::TYPE_BOOLEAN));
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__EnumValue';
    }
}
