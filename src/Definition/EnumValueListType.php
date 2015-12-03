<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;


use Youshido\GraphQL\Type\ListType\AbstractListType;

class EnumValueListType extends AbstractListType
{

    function getName()
    {
        return '__EnumValueList';
    }

    public function getItem()
    {
        return new EnumValueType();
    }

    public function resolve($value = null, $args = [])
    {
        // TODO: Implement resolve() method.
    }
}