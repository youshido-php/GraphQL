<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;


use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\ListType\AbstractListType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;

class InputValueListType extends AbstractListType
{

    public function getItem()
    {
        return new InputValueType();
    }

    public function resolve($value = null, $args = [])
    {
        if($value instanceof Field) {
            /** @var $value Field */
            if ($value->getConfig()->getType() instanceof AbstractScalarType) {
                return [];
            }

            return $value->getConfig()->getType()->getConfig()->getArguments() ?: [];
        } else {
            if ($value instanceof AbstractScalarType) {
                return null;
            }

            return $value->getConfig()->getArguments() ?: null;
        }
    }
}
