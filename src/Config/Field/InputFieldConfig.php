<?php

namespace Youshido\GraphQL\Config\Field;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class InputFieldConfig
 *
 * @method $this setDescription(string $description)
 */
class InputFieldConfig extends AbstractConfig
{
    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'name'              => ['type' => TypeService::TYPE_STRING, 'final' => true],
            'type'              => ['type' => TypeService::TYPE_ANY_INPUT, 'final' => true],
            'defaultValue'      => ['type' => TypeService::TYPE_ANY],
            'description'       => ['type' => TypeService::TYPE_STRING],
            'isDeprecated'      => ['type' => TypeService::TYPE_BOOLEAN],
            'deprecationReason' => ['type' => TypeService::TYPE_STRING],
        ];
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->get('defaultValue');
    }
}
