<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;


use Youshido\GraphQL\AbstractSchema;
use Youshido\GraphQL\Introspection\Traits\TypeCollectorTrait;
use Youshido\GraphQL\Type\ListType\AbstractListType;

class QueryListType extends AbstractListType
{
    use TypeCollectorTrait;

    public function getItemType()
    {
        return new QueryType();
    }

    public function resolve($value = null, $args = [], $type = null)
    {
        $this->types = [];

        /** @var $value AbstractSchema $a */
        $this->collectTypes($value->getQueryType());
        if ($value->getMutationType()->hasFields()) {
            $this->collectTypes($value->getMutationType());
        }

        return array_values($this->types);
    }
}
