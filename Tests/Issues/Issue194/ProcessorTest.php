<?php

namespace Youshido\Tests\Issue194;

use PHPUnit\Framework\TestCase;
use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class ProcessorTest extends TestCase
{

    public function testNonNullDefaultValue()
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'currentUser' => [
                        'type'    => new StringType(),
                        'args'    => [
                            'age' => [
                                'type'         => new IntType(),
                                'defaultValue' => 20,
                            ],
                        ],
                        'resolve' => static function ($source, $args, ResolveInfo $info) {
                            return 'Alex age ' . $args['age'];
                        },
                    ],
                ],
            ]),
        ]);

        $processor = new Processor($schema);

        $this->assertEquals(['data' => ['currentUser' => 'Alex age 20']],
            $processor->processPayload('{ currentUser }')->getResponseData());

        $this->assertEquals(['data' => ['currentUser' => 'Alex age 10']],
            $processor->processPayload('{ currentUser(age:10) }')->getResponseData());
    }

    public function testNullDefaultValue()
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'currentUser' => [
                        'type'    => new StringType(),
                        'args'    => [
                            'age' => [
                                'type'         => new IntType(),
                                'defaultValue' => null,
                            ],
                        ],
                        'resolve' => static function ($source, $args, ResolveInfo $info) {
                            if ($args['age'] === null) {
                                $args['age'] = 25;
                            }

                            return 'Alex age ' . $args['age'];
                        },
                    ],
                ],
            ]),
        ]);

        $processor = new Processor($schema);

        $this->assertEquals(['data' => ['currentUser' => 'Alex age 25']],
            $processor->processPayload('{ currentUser }')->getResponseData());

        $this->assertEquals(['data' => ['currentUser' => 'Alex age 10']],
            $processor->processPayload('{ currentUser(age:10) }')->getResponseData());
    }
}
