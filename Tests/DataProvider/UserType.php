<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/30/15 12:44 AM
*/

namespace Youshido\Tests\DataProvider;

use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class UserType extends AbstractObjectType
{

    public function build(TypeConfigInterface $config)
    {
        $config
            ->addField('id', TypeMap::TYPE_INT)
            ->addField('name', TypeMap::TYPE_STRING);

        $config
            ->addArgument('id', TypeMap::TYPE_INT, [
                'required' => true
            ]);
    }


    public function resolve($value = null, $args = [], $type = null)
    {
        return [
            'id'   => 1,
            'name' => 'John'
        ];
    }

    public function getName()
    {
        return "user";
    }

}