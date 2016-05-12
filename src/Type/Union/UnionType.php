<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11:54 AM 5/5/16
 */

namespace Youshido\GraphQL\Type\Union;

use Youshido\GraphQL\Type\TypeService;

final class UnionType extends AbstractUnionType
{

    public function resolveType($object)
    {
        return TypeService::resolveNamedType($object);
    }

    public function getTypes()
    {
        return $this->getConfig()->get('types', []);
    }

}
