<?php

namespace Youshido\GraphQL\Introspection\Field;

use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Introspection\QueryType;
use Youshido\GraphQL\Introspection\Traits\TypeCollectorTrait;
use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

/**
 * Class TypesField
 */
class TypesField extends AbstractField
{
    use TypeCollectorTrait;

    /**
     * @return AbstractObjectType
     */
    public function getType()
    {
        return new ListType(new QueryType());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'types';
    }

    /**
     * @param mixed       $value
     * @param array       $args
     * @param ResolveInfo $info
     *
     * @return array
     */
    public function resolve($value, array $args, ResolveInfo $info)
    {
        /** @var $value AbstractSchema $a */
        $this->types = [];
        $this->collectTypes($value->getQueryType());

        if ($value->getMutationType()->hasFields()) {
            $this->collectTypes($value->getMutationType());
        }

        foreach ($value->getTypes()->all() as $type) {
            $this->collectTypes($type);
        }

        return array_values($this->types);
    }
}
