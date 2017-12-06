<?php

namespace Youshido\GraphQL\Introspection\Field;

use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Introspection\SchemaType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class SchemaField
 */
class SchemaField extends AbstractField
{
    /**
     * @return AbstractObjectType
     */
    public function getType()
    {
        return new SchemaType();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '__schema';
    }

    /**
     * @param mixed       $value
     * @param array       $args
     * @param ResolveInfo $info
     *
     * @return \Youshido\GraphQL\Schema\AbstractSchema
     */
    public function resolve($value, array $args, ResolveInfo $info)
    {
        return $info->getExecutionContext()->getSchema();
    }
}
