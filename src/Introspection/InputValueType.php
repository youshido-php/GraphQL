<?php

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeMap;

/**
 * Class InputValueType
 */
class InputValueType extends AbstractObjectType
{
    /**
     * @param AbstractSchema|Field $value
     *
     * @return TypeInterface
     */
    public function resolveType($value)
    {
        return $value->getConfig()->getType();
    }

    /**
     * @param AbstractSchema|Field $value
     *
     * @return string|null
     */
    public function resolveDefaultValue($value)
    {
        $resolvedValue = $value->getConfig()->getDefaultValue();

        return $resolvedValue === null ? $resolvedValue : str_replace('"', '', json_encode($resolvedValue));
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
            ->addField(new Field([
                'name'    => 'type',
                'type'    => new NonNullType(new QueryType()),
                'resolve' => [$this, 'resolveType'],
            ]))
            ->addField('defaultValue', [
                'type'    => TypeMap::TYPE_STRING,
                'resolve' => [$this, 'resolveDefaultValue'],
            ]);
    }

    /**
     * @return string type name
     */
    public function getName()
    {
        return '__InputValue';
    }
}
