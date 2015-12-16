<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;

use Youshido\GraphQL\Definition\Traits\TypeCollectorTrait;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Definition\Traits\SchemaContainableTrait;
use Youshido\GraphQL\Type\TypeMap;

class TypeDefinitionType extends QueryType
{

    use SchemaContainableTrait, TypeCollectorTrait;

    public function resolve($value = null, $args = [])
    {
        $this->collectTypes($this->getSchema()->getQueryType());
        $this->collectTypes($this->getSchema()->getMutationType());

        foreach ($this->types as $name => $type) {
            if ($name == $args['name']) {
                return $type;
            }
        }

        return null;
    }

    /**
     * @return String type name
     */
    public function getName()
    {
        return '__Type';
    }

    protected function build(TypeConfigInterface $config)
    {
        parent::build($config);

        $config->addArgument('name', TypeMap::TYPE_STRING, ['required' => true]);
    }
}