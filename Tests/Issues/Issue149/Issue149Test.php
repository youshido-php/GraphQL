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

namespace Youshido\Tests\Issues\Issue116Test;

use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class Issue149Test extends \PHPUnit_Framework_TestCase
{
    public function testInternalVariableArgument(): void
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => [
                        'type' => new ObjectType([
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
                        'resolve' => static function () {
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
            'id'      => '1',
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
        ]]], $response);
    }
}
