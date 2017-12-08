<?php

namespace Youshido\GraphQL\Config\Directive;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\ArgumentsAwareConfigTrait;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class DirectiveConfig
 */
class DirectiveConfig extends AbstractConfig
{
    use ArgumentsAwareConfigTrait;

    /**
     * @var array
     */
    protected $locations = [];

    /**
     * @return array
     */
    public function getRules()
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'final' => true],
            'description' => ['type' => TypeService::TYPE_STRING],
            'args'        => ['type' => TypeService::TYPE_ARRAY],
            'locations'   => ['type' => TypeService::TYPE_ARRAY],
        ];
    }

    /**
     * @return array
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Configure class properties
     */
    public function build()
    {
        $this->buildArguments();

        if (!empty($this->data['locations'])) {
            foreach ($this->data['locations'] as $location) {
                $this->locations[] = $location;
            }
        }
    }
}
