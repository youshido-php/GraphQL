<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/30/15 12:44 AM
*/

namespace Youshido\Tests\DataProvider;

use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class TestObjectType extends AbstractObjectType
{

    public function build($config)
    {
        $config
            ->addField('id', new IntType())
            ->addField('name', new StringType())
            ->addField('region', new ObjectType([
                'name'   => 'Region',
                'fields' => [
                    'country' => new StringType(),
                    'city'    => new StringType()
                ],
            ]));
    }

    public function getInterfaces()
    {
        return [new TestInterfaceType()];
    }

    public function getData()
    {
        return [
            'id'   => 1,
            'name' => 'John'
        ];
    }

}
