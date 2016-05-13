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
use Youshido\GraphQL\Type\TypeService;

class UnionTypeConfig extends AbstractConfig implements TypeConfigInterface
{
    use FieldsAwareTrait, ArgumentsAwareTrait;

    public function getRules()
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'required' => true],
            'types'       => ['type' => TypeService::TYPE_ARRAY_OF_GRAPHQL_TYPES],
            'description' => ['type' => TypeService::TYPE_STRING],
            'resolveType' => ['type' => TypeService::TYPE_CALLABLE, 'final' => true]
        ];
    }
}
