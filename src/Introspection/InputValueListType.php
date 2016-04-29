<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;


use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\ListType\AbstractListType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\TypeMap;

class InputValueListType extends AbstractListType
{

    public function getItem()
    {
        return new InputValueType();
    }

    public function resolve($value = null, $args = [])
    {
        if ($value instanceof Field) {
            /** @var $value Field */
            if ($value->getConfig()->getType() instanceof AbstractScalarType) {
                return [];
            }

            if ($value->getConfig()->getType()->getKind() == TypeMap::KIND_INPUT_OBJECT) {
                return $value->getConfig()->getType()->getConfig()->getFields() ?: [];
            } else {
                return $value->getConfig()->getType()->getConfig() ? $value->getConfig()->getType()->getConfig()->getArguments() : [];
            }
        } else {
            if ($value instanceof AbstractScalarType) {
                return null;
            }

            if ($value->getKind() == TypeMap::KIND_INPUT_OBJECT) {
                return $value->getConfig()->getFields() ?: null;
            } else {
                return $value->getConfig()->getArguments() ?: null;
            }
        }
    }
}
