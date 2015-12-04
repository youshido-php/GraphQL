<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;


class InterfaceListType extends QueryListType
{

    public function getName()
    {
        return '__InterfaceList';
    }

    public function getItem()
    {
        return new InterfaceType();
    }

    public function resolve($value = null, $args = [])
    {
        return [];
    }


}