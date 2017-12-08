<?php

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Type\TypeService;

/**
 * Class InputObjectTypeConfig
 */
class InputObjectTypeConfig extends ObjectTypeConfig
{
    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'required' => true],
            'fields'      => ['type' => TypeService::TYPE_ARRAY_OF_INPUT_FIELDS, 'final' => true],
            'description' => ['type' => TypeService::TYPE_STRING],
        ];
    }
}
