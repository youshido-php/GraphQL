<?php

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\ArgumentsAwareConfigTrait;
use Youshido\GraphQL\Config\Traits\FieldsAwareConfigTrait;
use Youshido\GraphQL\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class EnumTypeConfig
 */
class EnumTypeConfig extends AbstractConfig implements TypeConfigInterface
{
    use FieldsAwareConfigTrait, ArgumentsAwareConfigTrait;

    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'final' => true],
            'description' => ['type' => TypeService::TYPE_STRING],
            'values'      => ['type' => TypeService::TYPE_ENUM_VALUES, 'required' => true],
        ];
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->get('values', []);
    }
}
