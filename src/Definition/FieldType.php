<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;


use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class FieldType extends AbstractObjectType
{

    protected function build(TypeConfigInterface $config)
    {
        $config
            ->addField('name', 'string')
            ->addField('description', 'string')
            ->addField('isDeprecated', 'boolean')
            ->addField('deprecationReason', 'string')
            ->addField('type', new QueryType())
            ->addField('args', new ArgumentListType());
    }

    public function resolve($value = null, $args = [])
    {
        /** @var $value Field */
        return $value->getType();
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Field';
    }
}