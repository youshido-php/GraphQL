<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;

use Youshido\GraphQL\Type\ListType\AbstractListType;

class ArgumentListType extends AbstractListType
{


    public function resolve($value = null, $args = [])
    {
        // TODO: Implement resolve() method.
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__ArgumentList';
    }

    public function getItem()
    {
        return new ArgumentType();
    }
}