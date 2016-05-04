<?php
/**
 * Date: 16.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;


use Youshido\GraphQL\Type\ListType\AbstractListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class DirectiveListType extends AbstractListType
{

    /**
     * @return AbstractObjectType
     */
    public function getItem()
    {
        return new DirectiveType();
    }

    public function resolve($value = null, $args = [], $type = null)
    {
        return null;
    }
}