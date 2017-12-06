<?php

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\ArgumentsAwareConfigTrait;
use Youshido\GraphQL\Config\Traits\FieldsAwareConfigTrait;
use Youshido\GraphQL\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class UnionTypeConfig
 */
class UnionTypeConfig extends AbstractConfig implements TypeConfigInterface
{
    use FieldsAwareConfigTrait, ArgumentsAwareConfigTrait;

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
