<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace graphql\src\Definition;


use Youshido\GraphQL\Definition\SchemaType;
use Youshido\GraphQL\Definition\TypeDefinitionType;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class DefinitionType extends AbstractObjectType
{

    protected function build(TypeConfigInterface $config)
    {
        $config
            ->addField('__schema', new SchemaType())
            ->addField('__type', new TypeDefinitionType());
    }

    public function resolve($value = null, $args = [])
    {
        // TODO: Implement resolve() method.
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        // TODO: Implement getName() method.
    }
}