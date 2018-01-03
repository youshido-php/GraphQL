<?php

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\FieldsAwareConfigTrait;
use Youshido\GraphQL\Validator\ConfigValidator\PropertyType;

/**
 * Class InterfaceTypeConfig
 *
 * @method void setName(string $name)
 * @method string getName()
 *
 * @method void setDescription(string $description)
 * @method string|null getDescription()
 *
 * @method callable getResolveType()
 * @method void setResolveType(callable $resolveType)
 */
class InterfaceTypeConfig extends AbstractConfig
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
            'resolveType' => ['type' => PropertyType::TYPE_CALLABLE, 'required' => true],
            'description' => ['type' => PropertyType::TYPE_STRING],
        ];
    }

    /**
     * Configure class properties
     */
    protected function build()
    {
        $this->buildFields();
    }
}
