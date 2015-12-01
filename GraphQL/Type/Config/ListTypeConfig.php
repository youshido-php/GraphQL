<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\Config;


use Youshido\GraphQL\Type\TypeMap;

class ListTypeConfig extends Config
{
    public function getRules()
    {
        return [
            'name'    => ['type' => TypeMap::TYPE_STRING, 'required' => true],
            'type'    => ['type' => TypeMap::TYPE_OBJECT_TYPE, 'required' => true],
            'resolve' => ['type' => TypeMap::TYPE_FUNCTION],
        ];
    }
}