<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;


use Youshido\GraphQL\Type\ListType\AbstractListType;

class FieldListType extends AbstractListType
{

    function getName()
    {
        return '__FieldList';
    }

    public function getItem()
    {
        new FieldType();
    }

    public function resolve($value = null, $args = [])
    {
        // TODO: Implement resolve() method.
    }
}