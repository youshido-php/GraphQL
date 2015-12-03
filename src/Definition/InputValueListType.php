<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;


use Youshido\GraphQL\Type\ListType\AbstractListType;

class InputValueListType extends AbstractListType
{

    function getName()
    {
        return '____InputValuesList';
    }

    public function getItem()
    {
        return new InputValueType();
    }

    public function resolve($value = null, $args = [])
    {
        // TODO: Implement resolve() method.
    }
}