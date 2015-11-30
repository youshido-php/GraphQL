<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/30/15 12:44 AM
*/

namespace Youshido\tests;


use Youshido\GraphQL\Type\Config\ObjectTypeConfig;
use Youshido\GraphQL\Type\Object\ObjectType;

class UserType extends ObjectType
{

    public function buildFields(ObjectTypeConfig $config)
    {
        $config
            ->addField('id', 'int')
            ->addField('name', 'string');
    }

    public function resolve($args = [])
    {
        return [
            'id'   => '1',
            'name' => 'John'
        ];
    }

    public function getName()
    {
        return "user";
    }

}