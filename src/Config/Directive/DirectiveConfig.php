<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/**
 * Date: 03/17/2017.
 */

namespace Youshido\GraphQL\Config\Directive;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\ArgumentsAwareConfigTrait;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class DirectiveConfig.
 */
class DirectiveConfig extends AbstractConfig
{
    use ArgumentsAwareConfigTrait;

    protected $locations = [];

    public function getRules()
    {
        return [
            'name'        => ['type' => TypeService::TYPE_STRING, 'final' => true],
            'description' => ['type' => TypeService::TYPE_STRING],
            'args'        => ['type' => TypeService::TYPE_ARRAY],
            'locations'   => ['type' => TypeService::TYPE_ARRAY],
        ];
    }

    public function getLocations()
    {
        return $this->locations;
    }

    public function build(): void
    {
        $this->buildArguments();

        if (!empty($this->data['locations'])) {
            foreach ($this->data['locations'] as $location) {
                $this->locations[] = $location;
            }
        }
    }
}
