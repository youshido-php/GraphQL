<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 6:17 PM 5/5/16
 */

namespace Examples\Blog\Schema;


use Youshido\GraphQL\Type\Object\AbstractUnionType;
use Youshido\GraphQL\Type\TypeMap;

class BlockContentUnion extends AbstractUnionType
{
    public function getTypes()
    {
        return [new PostType(), new BannerType()];
    }

    public function resolveType($object)
    {
        return TypeMap::getNamedType($object);
    }

}