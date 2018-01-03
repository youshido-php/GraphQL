<?php

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeInterface;

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
            ->addField('name', new NonNullType(new StringType()))
            ->addField('description', new StringType())
            ->addField('isDeprecated', new NonNullType(new BooleanType()))
            ->addField('deprecationReason', new StringType())
            ->addField(new Field([
                'name'    => 'type',
                'type'    => new NonNullType(new QueryType()),
                'resolve' => [$this, 'resolveType'],
            ]))
            ->addField('defaultValue', [
                'type'    => new StringType(),
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
