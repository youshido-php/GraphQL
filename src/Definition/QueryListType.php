<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;


use Youshido\GraphQL\Type\ListType\AbstractListType;

class QueryListType extends AbstractListType
{

    function getName()
    {
        return '__TypeList';
    }

    public function getItem()
    {
        return new QueryType();
    }

    public function resolve($value = null, $args = [])
    {
        // TODO: Implement resolve() method.
    }
}