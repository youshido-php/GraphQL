<?php

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\FieldsAwareConfigTrait;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Validator\ConfigValidator\PropertyType;

/**
 * Class ObjectTypeConfig
 *
 * //todo post validate interfaces
 *
 * @method void setName(string $name)
 * @method string getName()
 *
 * @method void setDescription(string $description)
 * @method string|null getDescription()
 *
 * @method AbstractInterfaceType[] getInterfaces()
 * @method void setInterfaces(array $interfaces)
 */
class ObjectTypeConfig extends AbstractConfig
{
    use FieldsAwareConfigTrait;

    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'name'        => ['type' => PropertyType::TYPE_STRING, 'required' => true],
            'fields'      => ['type' => PropertyType::TYPE_FIELD, 'required' => true, 'array' => true],
            'description' => ['type' => PropertyType::TYPE_STRING],
            'interfaces'  => ['type' => PropertyType::TYPE_INTERFACE, 'default' => [], 'array' => true],
        ];
    }

    /**
     * Add fields from passed interface
     *
     * @param AbstractInterfaceType $interfaceType
     *
     * @return $this
     */
    public function applyInterface(AbstractInterfaceType $interfaceType)
    {
        $this->addFields($interfaceType->getFields());

        return $this;
    }

    /**
     * Configure class properties
     */
    protected function build()
    {
        $this->buildFields();
    }
}
