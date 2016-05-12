<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/30/15 12:44 AM
*/

namespace Youshido\Tests\DataProvider;

use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class TestObjectType extends AbstractObjectType
{

    public function build($config)
    {
        $config
            ->addField('id', TypeMap::TYPE_INT)
            ->addField('name', TypeMap::TYPE_STRING);
    }


    public function resolve($value = null, $args = [], $type = null)
    {
        return [
            'id'   => 1,
            'name' => 'John'
        ];
    }

}
