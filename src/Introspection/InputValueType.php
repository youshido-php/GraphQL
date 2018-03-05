<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/**
 * Date: 03.12.15.
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeMap;

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
     *
     * //todo implement value printer
     */
    public function resolveDefaultValue($value)
    {
        $resolvedValue = $value->getConfig()->getDefaultValue();

        return null === $resolvedValue ? $resolvedValue : \str_replace('"', '', \json_encode($resolvedValue));
    }

    public function build($config): void
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
