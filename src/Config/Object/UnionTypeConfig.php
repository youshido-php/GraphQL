<?php

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class UnionTypeConfig
 */
class UnionTypeConfig extends AbstractConfig
{
    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'required' => true],
            'types'       => ['type' => TypeService::TYPE_ARRAY_OF_OBJECT_TYPES],
            'description' => ['type' => TypeService::TYPE_STRING],
            'resolveType' => ['type' => TypeService::TYPE_CALLABLE, 'final' => true],
        ];
    }
}
