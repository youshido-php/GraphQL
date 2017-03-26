<?php

namespace Youshido\Tests\Issues\Issue116Test;

use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class Issue116Test extends \PHPUnit_Framework_TestCase
{

    public function testInternalVariableArgument()
    {
        $schema    = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'connections' => [
                        'type'    => new ObjectType([
                            'name'   => 'ConnectionType',
                            'fields' => [
                                'pageInfo' => new ObjectType([
                                    'name'   => 'PageInfo',
                                    'fields' => [
                                        'totalEdges' => new IntType(),
                                        'cursors'    => [
                                            'type'    => new ListType(new StringType()),
                                            'args'    => [
                                                'size' => new NonNullType(new IntType()),
                                            ],
                                            'resolve' => function ($source, $args) {
                                                $res = [];
                                                foreach (range(1, $args['size']) as $i) {
                                                    $res[] = 'Cursor #' . $i;
                                                }

                                                return $res;
                                            }
                                        ],
                                    ]
                                ])
                            ]
                        ]),
                        'args'    => [
                            'first' => new IntType(),
                        ],
                        'resolve' => function () {
                            return [
                                'pageInfo' => [
                                    'totalEdges' => 10,
                                    'cursors'    => []
                                ]
                            ];
                        }
                    ]
                ]
            ])
        ]);
        $processor = new Processor($schema);
        $response  = $processor->processPayload('
query ($size: Int) {
  connections(first: 0) {
    pageInfo {
      totalEdges
      cursors (size: $size)
    }
  }
}',
            [
                'size' => 2,
            ])->getResponseData();
        $this->assertEquals(['data' => ['connections' => [
            'pageInfo' => [
                'totalEdges' => 10,
                'cursors'    => [
                    'Cursor #1', 'Cursor #2'
                ]
            ],
        ]]], $response);
    }
}