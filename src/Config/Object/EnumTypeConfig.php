<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/5/15 12:18 AM
*/

namespace Youshido\GraphQL\Config\Object;


use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\ArgumentsAwareTrait;
use Youshido\GraphQL\Config\Traits\FieldsAwareTrait;
use Youshido\GraphQL\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;

class EnumTypeConfig extends AbstractConfig implements TypeConfigInterface
{
    use FieldsAwareTrait, ArgumentsAwareTrait;

    public function getRules()
    {
        return [
            'name'        => ['type' => TypeMap::TYPE_STRING, 'required' => true],
            'description' => ['type' => TypeMap::TYPE_STRING],
            'values'      => ['type' => TypeService::TYPE_ARRAY_OF_VALUES, 'required' => true],
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
