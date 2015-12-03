<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\Config\Object;


use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\TypeMap;

class ListTypeConfig extends ObjectTypeConfig
{
    public function getRules()
    {
        return [
            'name'    => ['type' => TypeMap::TYPE_STRING],
            'item'    => ['type' => TypeMap::TYPE_OBJECT_TYPE, 'required' => true],
            'resolve' => ['type' => TypeMap::TYPE_FUNCTION],
            'args'    => ['type' => TypeMap::TYPE_ARRAY_OF_INPUTS],
        ];
    }

    /**
     * @param $name
     * @return Field
     */
    public function getField($name)
    {
        return $this->getItemConfig()->getField($name);
    }

    public function hasField($name)
    {
        return $this->getItemConfig()->hasField($name);
    }

    public function getFields()
    {
        return $this->getItemConfig()->getFields();
    }

    /**
     * @return ObjectTypeConfig
     */
    public function getItemConfig()
    {
        return $this->get('item')->getConfig();
    }
}