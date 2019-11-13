<?php

namespace Youshido\Tests\Issues\Issue116Test;

use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class Issue149Test extends \PHPUnit_Framework_TestCase
{
    public function testInternalVariableArgument()
    {
        $schema    = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => [
                        'type'    => new ObjectType([
                            'name'   => 'User',
                            'fields' => [
                                'id'      => new IdType(),
                                'name'    => new StringType(),
                                'age'     => new IntType(),
                                'friends' => new ListType(new ObjectType([
                                    'name'   => 'UserFriend',
                                    'fields' => [
                                        'id'   => new IdType(),
                                        'name' => new StringType(),
                                        'age'  => new IntType(),
                                    ],
                                ])),
                            ],
                        ]),
                        'resolve' => function () {
                            return [
                                'id'      => 1,
                                'name'    => 'John',
                                'age'     => 30,
                                'friends' => [
                                    [
                                        'id'   => 2,
                                        'name' => 'Friend 1',
                                        'age'  => 31,
                                    ],
                                    [
                                        'id'   => 3,
                                        'name' => 'Friend 2',
                                        'age'  => 32,
                                    ],
                                ],
                            ];
                        },
                    ],
                ],
            ]),
        ]);
        $processor = new Processor($schema);
        $response  = $processor->processPayload('
{
    user {
        id
        name
        friends {
            id
            name
        }
    }
    user {
        id
        age
        friends {
            id
            age
        }
    }
}')->getResponseData();
        $this->assertEquals(['data' => ['user' => [
            'id'   => '1',
            'name' => 'John',
            'age'  => 30,
            'friends' => [
                [
                    'id'   => 2,
                    'name' => 'Friend 1',
                    'age'  => 31,
                ],
                [
                    'id'   => 3,
                    'name' => 'Friend 2',
                    'age'  => 32,
                ],
            ]
        ]]], $response);
    }
}