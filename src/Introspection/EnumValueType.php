<?php

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\StringType;

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
            ->addField('name', new NonNullType(new StringType()))
            ->addField('description', new StringType())
            ->addField('deprecationReason', new StringType())
            ->addField('isDeprecated', new NonNullType(new BooleanType()));
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__EnumValue';
    }
}
