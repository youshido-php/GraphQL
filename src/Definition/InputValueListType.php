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

    public function getName()
    {
        return '__InputValuesList';
    }

    public function getItem()
    {
        return new InputValueType();
    }

    public function resolve($value = null, $args = [])
    {
        return [];
    }
}