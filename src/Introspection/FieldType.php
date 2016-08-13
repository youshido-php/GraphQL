<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class FieldType extends AbstractObjectType
{

    public static function resolveType(AbstractField $value)
    {
        return $value->getType();
    }

    public static function resolveArgs(AbstractField $value)
    {
        if ($value->getConfig()->hasArguments()) {
            return $value->getConfig()->getArguments();
        }

        return [];
    }

    public function build($config)
    {
        $config
            ->addField('name', TypeMap::TYPE_STRING)
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('isDeprecated', TypeMap::TYPE_BOOLEAN)
            ->addField('deprecationReason', TypeMap::TYPE_STRING)
            ->addField('type', [
                'type'    => new QueryType(),
                'resolve' => [get_class($this), 'resolveType'],
            ])
            ->addField('args', [
                'type'    => new ListType(new InputValueType()),
                'resolve' => [get_class($this), 'resolveArgs'],
            ]);
    }

    public function isValidValue($value)
    {
        return $value instanceof AbstractField;
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Field';
    }
}
