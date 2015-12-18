<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;


use Youshido\GraphQL\Type\TypeMap;

class InterfaceListType extends QueryListType
{

    public function getItem()
    {
        return new QueryType();
    }

    public function resolve($value = null, $args = [])
    {
        if ($value->getKind() == TypeMap::KIND_OBJECT) {
            return $value->getConfig()->getInterfaces() ?: [];
        } elseif ($value->getKind() == TypeMap::KIND_UNION) {
            return null;
        }

        return [];
    }
}
