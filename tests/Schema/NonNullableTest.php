<?php
/**
 * Date: 03.11.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Schema;

use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class uid
{
    private $uid;

    public function __construct($uid)
    {
        $this->uid = $uid;
    }

    public function __toString()
    {
        return $this->uid;
    }
}

class NonNullableTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider queries
     *
     * @param $query
     * @param $expected
     */
    public function testNullableResolving($query, $expected)
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'nonNullScalar'        => [
                        'type'    => new NonNullType(new IntType()),
                        'resolve' => function () {
                            return null;
                        },
                    ],
                    'nonNullList'          => [
                        'type'    => new NonNullType(new ListType(new IntType())),
                        'resolve' => function () {
                            return null;
                        }
                    ],
                    'user'                 => [
                        'type'    => new NonNullType(new ObjectType([
                            'name'   => 'User',
                            'fields' => [
                                'id'   => new NonNullType(new IdType()),
                                'name' => new StringType(),
                            ]
                        ])),
                        'resolve' => function () {
                            return [
                                'id'   => new uid('6cfb044c-9c0a-4ddd-9ef8-a0b940818db3'),
                                'name' => 'Alex'
                            ];
                        }
                    ],
                    'nonNullListOfNpnNull' => [
                        'type'    => new NonNullType(new ListType(new NonNullType(new IntType()))),
                        'resolve' => function () {
                            return [1, null];
                        }
                    ],
                    'nonNullArgument'     => [
                        'args'    => [
                            'ids' => new NonNullType(new ListType(new IntType()))
                        ],
                        'type'    => new IntType(),
                        'resolve' => function () {
                            return 1;
                        }
                    ],
                    'nonNullArgument2'     => [
                        'args'    => [
                            'ids' => new NonNullType(new ListType(new NonNullType(new IntType())))
                        ],
                        'type'    => new IntType(),
                        'resolve' => function () {
                            return 1;
                        }
                    ],
                ]
            ])
        ]);

        $processor = new Processor($schema);
        $processor->processPayload($query);
        $result = $processor->getResponseData();

        $this->assertEquals($expected, $result);
    }

    public function queries()
    {
        return [
            [
                '{ test:nonNullArgument2(ids: [1, 2]) }',
                [
                    'data' => [
                        'test' => 1
                    ]
                ],
            ],
            [
                '{ test:nonNullArgument2(ids: [1, null]) }',
                [
                    'data' => [
                        'test' => null
                    ],
                    'errors' => [
                        [
                            'message' => 'Not valid type for argument "ids" in query "nonNullArgument2": Field must not be NULL',
                            'locations' => [['line' => 1, 'column' => 25]]
                        ]
                    ]
                ],
            ],
            [
                '{ test:nonNullArgument(ids: [1, null]) }',
                [
                    'data' => [
                        'test' => 1
                    ]
                ]
            ],
            [
                '{ test:nonNullArgument }',
                [
                    'data' => [
                        'test' => null
                    ],
                    'errors' => [
                        [
                            'message' => 'Require "ids" arguments to query "nonNullArgument"'
                        ]
                    ]
                ]
            ],
            [
                '{ nonNullScalar  }',
                [
                    'data'   => [
                        'nonNullScalar' => null
                    ],
                    'errors' => [
                        [
                            'message' => 'Cannot return null for non-nullable field "nonNullScalar"'
                        ]
                    ]
                ]
            ],

            [
                '{ nonNullList  }',
                [
                    'data'   => [
                        'nonNullList' => null
                    ],
                    'errors' => [
                        [
                            'message' => 'Cannot return null for non-nullable field "nonNullList"'
                        ]
                    ]
                ]
            ],
            [
                '{ nonNullListOfNpnNull  }',
                [
                    'data'   => [
                        'nonNullListOfNpnNull' => null,
                    ],
                    'errors' => [
                        [
                            'message' => 'Not valid resolved type for field "nonNullListOfNpnNull": Field must not be NULL'
                        ]
                    ]
                ]
            ],

            [
                '{ user {id, name}  }',
                [
                    'data' => [
                        'user' => [
                            'id'   => '6cfb044c-9c0a-4ddd-9ef8-a0b940818db3',
                            'name' => 'Alex'
                        ]
                    ]
                ]
            ],
            [
                '{ user { __typename }  }',
                [
                    'data' => [
                        'user' => [
                            '__typename' => 'User'
                        ]
                    ]
                ]
            ]
        ];
    }

}
