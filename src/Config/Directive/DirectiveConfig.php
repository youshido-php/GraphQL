<?php
/**
 * Date: 03/17/2017
 *
 * @author Volodymyr Rashchepkin <rashepkin@gmail.com>
 */

namespace Youshido\GraphQL\Config\Directive;


use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\ArgumentsAwareConfigTrait;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class DirectiveConfig
 *
 * @package Youshido\GraphQL\Config\Directive
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
