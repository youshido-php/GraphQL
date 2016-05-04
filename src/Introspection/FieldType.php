<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\Object\AbstractMutationType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class FieldType extends AbstractObjectType
{

    public function build(TypeConfigInterface $config)
    {
        $config
            ->addField('name', TypeMap::TYPE_STRING)
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('isDeprecated', TypeMap::TYPE_BOOLEAN)
            ->addField('deprecationReason', TypeMap::TYPE_STRING)
            ->addField('type', new QueryType(), [
                'resolve' => function (Field $value) {
                    return $value->getType();
                }
            ])
            ->addField('args', new InputValueListType());
    }

    public function resolve($value = null, $args = [], $type = null)
    {
        /** @var $value Field */
        return $value->getType();
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Field';
    }
}
