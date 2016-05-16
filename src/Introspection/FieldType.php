<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\TypeMap;

class FieldType extends AbstractObjectType
{

    public function build($config)
    {
        $config
            ->addField('name', TypeMap::TYPE_STRING)
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('isDeprecated', TypeMap::TYPE_BOOLEAN)
            ->addField('deprecationReason', TypeMap::TYPE_STRING)
            ->addField('type', [
                'type'    => new QueryType(),
                'resolve' => function (AbstractField $value) {
                    return $value->getType()->getNamedType();
                }
            ])
            ->addField('args', [
                'type'    => new ListType(new InputValueType()),
                'resolve' => function ($value) {
                    /** @var $value Field */
                    if ($value->getConfig()->hasArguments()) {
                        return $value->getConfig()->getArguments();
                    }

                    return [];
                }
            ]);
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Field';
    }
}
