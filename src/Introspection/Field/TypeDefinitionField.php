<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection\Field;

use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Introspection\QueryType;
use Youshido\GraphQL\Introspection\Traits\TypeCollectorTrait;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class TypeDefinitionField extends AbstractField
{

    use TypeCollectorTrait;

    public function resolve($value = null, $args = [], $type = null)
    {
        $this->collectTypes(SchemaField::$schema->getQueryType());
        $this->collectTypes(SchemaField::$schema->getMutationType());

        foreach ($this->types as $name => $type) {
            if ($name == $args['name']) {
                return $type;
            }
        }

        return null;
    }

    public function build(FieldConfig $config)
    {
        $config->addArgument(new InputField([
            'name' => 'name',
            'type' => new NonNullType(new StringType())
        ]));
    }


    /**
     * @return String type name
     */
    public function getName()
    {
        return '__type';
    }

    /**
     * @return AbstractObjectType
     */
    public function getType()
    {
        return new QueryType();
    }
}
