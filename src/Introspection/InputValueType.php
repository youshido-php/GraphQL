<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class InputValueType extends AbstractObjectType
{

    public function build($config)
    {
        $config
            ->addField('name', TypeMap::TYPE_STRING)
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('type', new QueryType())
            ->addField('defaultValue', TypeMap::TYPE_STRING);
    }

    public function resolve($value = null, $args = [], $type = null)
    {
        return null;
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__InputValue';
    }
}