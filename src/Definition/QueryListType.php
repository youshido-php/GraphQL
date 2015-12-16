<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;


use Youshido\GraphQL\Definition\Traits\TypeCollectorTrait;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\ListType\AbstractListType;

class QueryListType extends AbstractListType
{
    use TypeCollectorTrait;

    public function getItem()
    {
        return new QueryType();
    }

    public function resolve($value = null, $args = [])
    {
        $this->types = [];

        /** @var $value Schema $a */
        $this->collectTypes($value->getQueryType());
        $this->collectTypes($value->getMutationType());

        return array_values($this->types);
    }
}
