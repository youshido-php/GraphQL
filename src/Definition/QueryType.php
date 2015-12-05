<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;

use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class QueryType extends AbstractObjectType
{

    protected function build(TypeConfigInterface $config)
    {
        $config
            ->addField('name', 'string')
            ->addField('kind', 'string')
            ->addField('description', 'string')
            ->addField('ofType', new QueryListType(), [
                'resolve' => function () {
                    return [];
                }
            ])
            ->addField('inputFields', new InputValueListType())
            ->addField('enumValues', new EnumValueListType())
            ->addField('fields', new FieldListType())
            ->addField('interfaces', new InterfaceListType())
            ->addField('possibleTypes', new PossibleOfListType());
    }

    public function resolve($value = null, $args = [])
    {
        /** @var Schema|Field $value */
        if ($value instanceof Schema) {
            return $value->getQueryType();
        }

        return $value->getConfig()->getType();
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Type';
    }
}