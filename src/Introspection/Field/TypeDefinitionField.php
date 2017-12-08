<?php

namespace Youshido\GraphQL\Introspection\Field;

use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Execution\TypeCollector;
use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Introspection\QueryType;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class TypeDefinitionField
 */
class TypeDefinitionField extends AbstractField
{
    /**
     * @param null        $value
     * @param array       $args
     * @param ResolveInfoInterface $info
     *
     * @return null|AbstractType
     */
    public function resolve($value = null, array $args, ResolveInfoInterface $info)
    {
        $collector = TypeCollector::getInstance();
        $collector->addSchema($info->getExecutionContext()->getSchema());

        foreach ($collector->getTypes() as $type) {
            if ($type->getName() === $args['name']) {
                return $type;
            }
        }

        return null;
    }

    /**
     * @param FieldConfig $config
     */
    public function build(FieldConfig $config)
    {
        $config->addArgument(new InputField([
            'name' => 'name',
            'type' => new NonNullType(new StringType()),
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
