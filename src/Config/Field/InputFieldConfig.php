<?php

namespace Youshido\GraphQL\Config\Field;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Validator\ConfigValidator\PropertyType;

/**
 * Class InputFieldConfig
 *
 * @method void setName(string $name)
 * @method string getName()
 *
 * @method void setDescription(string $description)
 * @method string|null getDescription()
 *
 * @method void setType(TypeInterface $type)
 * @method TypeInterface getType()
 *
 * @method void setDefaultValue(mixed $defaultValue)
 * @method mixed|null getDefaultValue()
 *
 * @method void setIsDeprecated(bool $isDeprecated)
 * @method bool isDeprecated()
 *
 * @method void setDeprecationReason(string $deprecationReason)
 * @method string getDeprecationReason()
 */
class InputFieldConfig extends AbstractConfig
{
    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'name'              => ['type' => PropertyType::TYPE_STRING, 'required' => true],
            'type'              => ['type' => PropertyType::TYPE_INPUT_TYPE, 'required' => true],
            'defaultValue'      => ['type' => PropertyType::TYPE_ANY],
            'description'       => ['type' => PropertyType::TYPE_STRING],
            'isDeprecated'      => ['type' => PropertyType::TYPE_BOOLEAN, 'default' => false],
            'deprecationReason' => ['type' => PropertyType::TYPE_STRING],
        ];
    }
}
