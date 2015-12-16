<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Definition\Traits\SchemaContainableTrait;
use Youshido\GraphQL\Definition\DirectiveListType;

class SchemaType extends AbstractObjectType
{

    use SchemaContainableTrait;

    public function resolve($value = null, $args = [])
    {
        return $this->getSchema();
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
            ->addField('mutationType', new MutationType())
            ->addField('types', new QueryListType())
            ->addField('directives', new DirectiveListType());
    }
}