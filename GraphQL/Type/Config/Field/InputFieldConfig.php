<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 11:28 PM
*/

namespace Youshido\GraphQL\Type\Config\Field;


use Youshido\GraphQL\Type\Config\Config;
use Youshido\GraphQL\Type\TypeMap;

class InputFieldConfig extends Config
{

    public function getRules()
    {
        return [
            'name'        => ['type' => TypeMap::TYPE_STRING, 'required' => true],
            'type'        => ['type' => TypeMap::TYPE_ANY_INPUT, 'required' => true],
            'default'     => ['type' => TypeMap::TYPE_ANY],
            'description' => ['type' => TypeMap::TYPE_STRING],
        ];
    }

    public function getDefaultValue()
    {
        return $this->get('default');
    }

}