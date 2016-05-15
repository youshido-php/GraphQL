<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\TypeMap;

class TypeDefinitionField extends QueryType
{

    public function resolve($value = null, $args = [], $type = null)
    {
        $this->collectTypes(SchemaType::$schema->getQueryType());
        $this->collectTypes(SchemaType::$schema->getMutationType());

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

    public function build($config)
    {
        parent::build($config);

        $config->addArgument('name', new NonNullType(TypeMap::TYPE_STRING));
    }
}
