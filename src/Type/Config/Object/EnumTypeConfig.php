<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/5/15 12:18 AM
*/

namespace Youshido\GraphQL\Type\Config\Object;


use Youshido\GraphQL\Type\Config\Config;
use Youshido\GraphQL\Type\Config\Traits\ArgumentsAwareTrait;
use Youshido\GraphQL\Type\Config\Traits\FieldsAwareTrait;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\TypeMap;

class EnumTypeConfig extends Config implements TypeConfigInterface
{
    use FieldsAwareTrait, ArgumentsAwareTrait;

    public function getRules()
    {
        return [
            'name'        => ['type' => TypeMap::TYPE_STRING, 'required' => true],
            'description' => ['type' => TypeMap::TYPE_STRING],
            'values'      => ['type' => TypeMap::TYPE_ARRAY_OF_VALUES, 'required' => true],
        ];
    }

    public function getValues()
    {
        return $this->get('values', []);
    }

    protected function build()
    {
    }
}