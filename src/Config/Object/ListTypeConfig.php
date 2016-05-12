<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 1:22 AM
*/

namespace Youshido\GraphQL\Config\Object;


use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\TypeService;

class ListTypeConfig extends ObjectTypeConfig
{
    public function getRules()
    {
        return [
            'item'    => ['type' => TypeService::TYPE_ANY, 'required' => true],
            'resolve' => ['type' => TypeService::TYPE_FUNCTION],
            'args'    => ['type' => TypeService::TYPE_ARRAY_OF_INPUTS],
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
        if ($itemConfig = $this->getItemConfig()) {
            return $itemConfig->getFields();
        } else {
            return [];
        }
    }

    /**
     * @return ObjectTypeConfig
     */
    public function getItemConfig()
    {
        return $this->getItem()->getConfig();
    }

    public function getItem()
    {
        return $this->get('item');
    }
}
