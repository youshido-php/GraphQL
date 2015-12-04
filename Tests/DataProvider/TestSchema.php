<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/3/15 11:28 PM
*/

namespace Youshido\Tests\DataProvider;

use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\ListType\ListType;

class TestSchema extends Schema
{
    public function buildTypes(TypeConfigInterface $config)
    {
        $config
            ->addField('users', new ListType(['item' => new UserType()]));
    }

    public function buildMutations(TypeConfigInterface $config)
    {
    }

    public function getName()
    {
        return 'TestSchema';
    }
}