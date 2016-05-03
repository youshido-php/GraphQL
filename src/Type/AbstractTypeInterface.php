<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 3:42 PM 5/3/16
 */

namespace Youshido\GraphQL\Type;


Interface AbstractTypeInterface
{
    /**
     * @param $object
     * @return AbstractType
     */
    public function resolveType($object);
}