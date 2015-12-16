<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class ArgumentType extends AbstractObjectType
{

    protected function build(TypeConfigInterface $config)
    {
        $config
            ->addField('name', TypeMap::TYPE_STRING)
            ->addField('type', new QueryType())
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('defaultValue', TypeMap::TYPE_STRING);
    }

    public function resolve($value = null, $args = [])
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