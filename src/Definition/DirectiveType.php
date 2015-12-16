<?php
/**
 * Date: 16.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class DirectiveType extends AbstractObjectType
{

    public function resolve($value = null, $args = [])
    {
        return null; //todo: not supported yet
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Directive';
    }

    protected function build(TypeConfigInterface $config)
    {
        $config
            ->addField('name', TypeMap::TYPE_STRING)
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('args', new InputValueListType())
            ->addField('onOperation', TypeMap::TYPE_BOOLEAN)
            ->addField('onFragment', TypeMap::TYPE_BOOLEAN)
            ->addField('onField', TypeMap::TYPE_BOOLEAN);
    }
}