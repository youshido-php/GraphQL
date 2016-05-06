<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Type\ListType\AbstractListType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;

class FieldListType extends AbstractListType
{

    public function getItemType()
    {
        return new FieldType();
    }

    public function resolve($value = null, $args = [], $type = null)
    {
        if (!$value || in_array($value->getKind(), [
                TypeMap::KIND_SCALAR,
                TypeMap::KIND_UNION,
                TypeMap::KIND_INPUT_OBJECT
            ])
        ) {
            return null;
        }

        /** @var ObjectType $value */
        $fields = $value->getConfig()->getFields();

        foreach ($fields as $key => $field) {
            if (in_array($field->getName(), ['__type', '__schema'])) {
                unset($fields[$key]);
            }
        }

        return $fields;
    }
}
