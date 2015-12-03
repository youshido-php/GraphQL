<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class QueryType extends AbstractObjectType
{

    protected function build(TypeConfigInterface $config)
    {
        $config
            ->addField('kind', 'string')
            ->addField('kind', 'string')
            ->addField('description', 'string')
            ->addField('ofType', new QueryType())
            ->addField('inputFields', new InputValueListType())
            ->addField('enumValues', new EnumValueListType())
            ->addField('fields', new FieldListType())
            ->addField('interfaces', new QueryListType())
            ->addField('possibleTypes', new QueryListType());
    }

    public function resolve($value = null, $args = [])
    {

    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Type';
    }
}