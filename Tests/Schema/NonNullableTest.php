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
use Youshido\GraphQL\Type\Scalar\IntType;

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
                    'nonNullScalar' => [
                        'type'    => new NonNullType(new IntType()),
                        'resolve' => function () {
                            return null;
                        },
                    ],

                    'nonNullList' => [
                        'type'    => new NonNullType(new ListType(new IntType())),
                        'resolve' => function () {
                            return null;
                        }
                    ],

                    'nonNullListOfNpnNull' => [
                        'type'    => new NonNullType(new ListType(new NonNullType(new IntType()))),
                        'resolve' => function () {
                            return [1, null];
                        }
                    ]
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
                '{ nonNullScalar  }',
                [
                    'data' => [
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
                    'data' => [
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
                    'data' => [
                        'nonNullListOfNpnNull' => [1, null]
                    ],
                    'errors' => [
                        [
                            'message' => 'Cannot return null for non-nullable field "nonNullListOfNpnNull"'
                        ]
                    ]
                ]
            ]
        ];
    }

}