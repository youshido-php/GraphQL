<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;


class PossibleOfListType extends QueryListType
{

    public function getItem()
    {
        return new PossibleOfType();
    }

    public function resolve($value = null, $args = [])
    {
        return [];
    }


}
