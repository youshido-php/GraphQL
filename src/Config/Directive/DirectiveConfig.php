<?php

namespace Youshido\GraphQL\Config\Directive;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\ArgumentsAwareConfigTrait;
use Youshido\GraphQL\Validator\ConfigValidator\PropertyType;

/**
 * Class DirectiveConfig
 *
 * @method void setName(string $name)
 * @method string getName()
 *
 * @method void setDescription(string | null $description)
 * @method string|null getDescription()
 *
 * @method array getLocations()
 * @method void setLocations(array $locations)
 */
class DirectiveConfig extends AbstractConfig
{
    use ArgumentsAwareConfigTrait;

    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'name'        => ['type' => PropertyType::TYPE_STRING, 'required' => true],
            'locations'   => ['type' => PropertyType::TYPE_LOCATION, 'array' => true, 'required' => true],
            'description' => ['type' => PropertyType::TYPE_STRING],
            'args'        => ['type' => PropertyType::TYPE_INPUT_FIELD, 'array' => true],
        ];
    }

    /**
     * Configure class properties
     */
    public function build()
    {
        $this->buildArguments();
    }
}
