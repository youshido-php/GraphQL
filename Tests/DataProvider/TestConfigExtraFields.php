<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/12/16 9:27 PM
*/

namespace Youshido\Tests\DataProvider;


use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Validator\ConfigValidator\PropertyType;

class TestConfigExtraFields extends AbstractConfig
{
    public function getRules()
    {
        return [
            'name' => ['type' => PropertyType::TYPE_ANY, 'required' => true]
        ];
    }
}
