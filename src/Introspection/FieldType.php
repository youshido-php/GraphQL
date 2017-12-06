<?php

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

/**
 * Class FieldType
 */
class FieldType extends AbstractObjectType
{
    /**
     * @param FieldInterface $value
     *
     * @return \Youshido\GraphQL\Type\AbstractType
     */
    public function resolveType(FieldInterface $value)
    {
        return $value->getType();
    }

    /**
     * @param FieldInterface $value
     *
     * @return array|\Youshido\GraphQL\Type\AbstractType[]
     */
    public function resolveArgs(FieldInterface $value)
    {
        if ($value->hasArguments()) {
            return $value->getArguments();
        }

        return [];
    }

    /**
     * @param \Youshido\GraphQL\Config\Object\ObjectTypeConfig $config
     */
    public function build($config)
    {
        $config
            ->addField('name', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('isDeprecated', new NonNullType(TypeMap::TYPE_BOOLEAN))
            ->addField('deprecationReason', TypeMap::TYPE_STRING)
            ->addField('type', [
                'type'    => new NonNullType(new QueryType()),
                'resolve' => [$this, 'resolveType'],
            ])
            ->addField('args', [
                'type'    => new NonNullType(new ListType(new NonNullType(new InputValueType()))),
                'resolve' => [$this, 'resolveArgs'],
            ]);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidValue($value)
    {
        return $value instanceof FieldInterface;
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Field';
    }
}
