<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 1:22 AM
*/

namespace Youshido\GraphQL\Config\Object;


use Youshido\GraphQL\Type\TypeService;

class NonNullTypeConfig extends ObjectTypeConfig
{
    public function getRules()
    {
        return [
            'item' => ['type' => TypeService::TYPE_GRAPHQL_TYPE, 'required' => true],
        ];
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
