<?php

namespace Youshido\Tests\DataProvider;


use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Validator\ConfigValidator\PropertyType;

class TestConfigInvalidRule extends AbstractConfig
{
    public function getRules()
    {
        return [
            'name'             => ['type' => PropertyType::TYPE_ANY, 'required' => true],
            'invalidRuleField' => ['type' => PropertyType::TYPE_ANY, 'invalid rule' => true]
        ];
    }

}
