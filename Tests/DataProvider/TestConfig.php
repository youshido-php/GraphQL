<?php

namespace Youshido\Tests\DataProvider;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Validator\ConfigValidator\PropertyType;

/**
 * Class TestConfig
 */
class TestConfig extends AbstractConfig
{
    public function getRules()
    {
        return [
            'name'    => ['type' => PropertyType::TYPE_ANY, 'required' => true],
            'resolve' => ['type' => PropertyType::TYPE_CALLABLE, 'required' => true],
        ];
    }
}
