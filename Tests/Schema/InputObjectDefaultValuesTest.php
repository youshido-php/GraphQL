<?php

namespace Youshido\Tests\Schema;

use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\Enum\EnumType;
use Youshido\GraphQL\Type\InputObject\InputObjectType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\DateTimeTzType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class InputObjectDefaultValuesTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaultEnum()
    {
        $enumType = new EnumType([
            'name'   => 'InternalStatus',
            'values' => [
                [
                    'name'  => 1,
                    'value' => 'ACTIVE'
                ],
                [
                    'name'  => 0,
                    'value' => 'DISABLED'
                ],
            ]
        ]);
        $schema   = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'stringQuery' => [
                        'type'    => new StringType(),
                        'args'    => [
                            'statObject' => new InputObjectType([
                                'name'   => 'StatObjectType',
                                'fields' => [
                                    'status' => [
                                        'type'    => $enumType,
                                        'default' => 1
                                    ],
                                    'level'  => new NonNullType(new IntType())
                                ]
                            ])
                        ],
                        'resolve' => function ($source, $args) {
                            return sprintf('Result with level %s and status %s',
                                $args['statObject']['level'], $args['statObject']['status']
                            );
                        },
                    ],
                ]
            ])
        ]);

        $processor = new Processor($schema);
        $processor->processPayload('{ stringQuery(statObject: { level: 1 }) }');
        $result = $processor->getResponseData();

        $this->assertEquals(['data' => [
            'stringQuery' => 'Result with level 1 and status ACTIVE'
        ]], $result);
    }

}