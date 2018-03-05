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
/*
 * This file is a part of graphql-youshido project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11/30/15 12:44 AM
 */

namespace Youshido\Tests\DataProvider;

use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class TestObjectType extends AbstractObjectType
{
    public function build($config): void
    {
        $config
            ->addField('id', new IntType())
            ->addField('name', new StringType())
            ->addField('region', new ObjectType([
                'name'   => 'Region',
                'fields' => [
                    'country' => new StringType(),
                    'city'    => new StringType(),
                ],
            ]))
            ->addField(
                'location',
                [
                 'type' => new ObjectType(
                     [
                         'name'   => 'Location',
                         'fields' => [
                             'address' => new StringType(),
                         ],
                     ]
                 ),
                 'args' => [
                     'noop' => new IntType(),
                 ],
                 'resolve' => static function ($value, $args, $info) {
                     return ['address' => '1234 Street'];
                 },
             ]
            )
            ->addField(
                'echo',
                [
                    'type' => new StringType(),
                    'args' => [
                        'value' => new NonNullType(new StringType()),
                    ],
                    'resolve' => static function ($value, $args, $info) {
                        return $args['value'];
                    },
                ]
            );
    }

    public function getInterfaces()
    {
        return [new TestInterfaceType()];
    }

    public function getData()
    {
        return [
            'id'   => 1,
            'name' => 'John',
        ];
    }
}
