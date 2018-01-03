<?php

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Validator\ConfigValidator\PropertyType;

/**
 * Class EnumTypeConfig
 *
 * @method void setName(string $name)
 * @method string getName()
 *
 * @method void setDescription(string $description)
 * @method string|null getDescription()
 *
 * @method array getValues()
 * @method void setValues(array $values)
 */
class EnumTypeConfig extends AbstractConfig
{
    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'name'        => ['type' => PropertyType::TYPE_STRING, 'required' => true],
            'values'      => ['type' => PropertyType::TYPE_ENUM_VALUES, 'required' => true],
            'description' => ['type' => PropertyType::TYPE_STRING],
        ];
    }
}
