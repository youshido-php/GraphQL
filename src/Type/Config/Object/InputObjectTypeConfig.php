<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 11:13 PM
*/

namespace Youshido\GraphQL\Type\Config\Object;


use Youshido\GraphQL\Type\TypeMap;

class InputObjectTypeConfig extends ObjectTypeConfig
{
    public function getRules()
    {
        return [
            'name'        => ['type' => TypeMap::TYPE_STRING, 'required' => true],
            'description' => ['type' => TypeMap::TYPE_STRING],
            'fields'      => ['type' => TypeMap::TYPE_ARRAY_OF_INPUTS],
        ];
    }
}