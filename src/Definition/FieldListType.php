<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;


use Youshido\GraphQL\Type\ListType\AbstractListType;
use Youshido\GraphQL\Type\Object\ObjectType;

class FieldListType extends AbstractListType
{

    public function getName()
    {
        return '__FieldList';
    }

    public function getItem()
    {
        return new FieldType();
    }

    public function resolve($value = null, $args = [])
    {
        /** @var ObjectType $value */
        return $value->getConfig()->getFields();
    }
}