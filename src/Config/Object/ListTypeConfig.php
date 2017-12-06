<?php

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Type\TypeService;

/**
 * Class ListTypeConfig
 */
class ListTypeConfig extends ObjectTypeConfig
{
    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'itemType' => ['type' => TypeService::TYPE_GRAPHQL_TYPE, 'final' => true],
            'resolve'  => ['type' => TypeService::TYPE_CALLABLE],
            'args'     => ['type' => TypeService::TYPE_ARRAY_OF_INPUT_FIELDS],
        ];
    }
}
