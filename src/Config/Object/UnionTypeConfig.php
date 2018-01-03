<?php

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Validator\ConfigValidator\PropertyType;

/**
 * Class UnionTypeConfig
 *
 * @method void setName(string $name)
 * @method string getName()
 *
 * @method void setDescription(string $description)
 * @method string|null getDescription()
 *
 * @method void setTypes(array $types)
 * @method AbstractObjectType[] getTypes()
 *
 * @method callable getResolveType()
 * @method void setResolveType(callable $resolveType)
 */
class UnionTypeConfig extends AbstractConfig
{
    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'name'        => ['type' => PropertyType::TYPE_STRING, 'required' => true],
            'types'       => ['type' => PropertyType::TYPE_OBJECT_TYPE, 'required' => true, 'array' => true],
            'resolveType' => ['type' => PropertyType::TYPE_CALLABLE, 'required' => true],
            'description' => ['type' => PropertyType::TYPE_STRING],
        ];
    }
}
