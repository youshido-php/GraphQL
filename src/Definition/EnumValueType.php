<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class EnumValueType extends AbstractObjectType
{

    protected function build(TypeConfigInterface $config)
    {
        $config
            ->addField('name', 'string')
            ->addField('description', 'string')
            ->addField('deprecationReason', 'string')
            ->addField('isDeprecated', 'boolean');
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
        return '__EnumValue';
    }
}