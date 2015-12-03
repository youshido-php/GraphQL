<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class SchemaType extends AbstractObjectType
{

    public function resolve($value = null, $args = [])
    {
        return $value;
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Schema';
    }

    protected function build(TypeConfigInterface $config)
    {
        $config
            ->addField('queryType', new QueryType())
            ->addField('mutationType', new QueryType())
            ->addField('types', new QueryListType());

        //todo: not realization at all
//            ->addField('directives', new DirectiveType());
    }
}