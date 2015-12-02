<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\Config;


use Youshido\GraphQL\Field;
use Youshido\GraphQL\Type\TypeMap;

class ListTypeConfig extends ObjectTypeConfig
{
    public function getRules()
    {
        return [
            'name'    => ['type' => TypeMap::TYPE_STRING, 'required' => true],
            'item'    => ['type' => TypeMap::TYPE_OBJECT_TYPE, 'required' => true],
            'resolve' => ['type' => TypeMap::TYPE_FUNCTION],
        ];
    }


    //todo: code below is not fully corrected, because type = int then error

    /**
     * @param $name
     * @return Field
     */
    public function getField($name)
    {
        return $this->data['item']->getField($name);
    }

    public function hasField($name)
    {
        return $this->data['item']->hasField($name);
    }

    public function getFields()
    {
        return $this->data['item']->getFields();
    }
}