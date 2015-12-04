<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;

class InterfaceType extends QueryType
{

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Interface';
    }
}