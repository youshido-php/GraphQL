<?php

namespace Youshido\Tests\Issues\Issue116Test;

use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\InputObject\InputObjectType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class Issue131Test extends \PHPUnit_Framework_TestCase
{

    public function testInternalVariableArgument()
    {


        $schema    = new Schema([
            'query'    => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'hello' => new StringType(),
                ]
            ]),
            'mutation' => new ObjectType([
                'name'   => 'RootMutation',
                'fields' => [
                    'createMeeting' => [
                        'type'    => new ObjectType([
                            'name'   => 'Meeting',
                            'fields' => [
                                'id'   => new IdType(),
                                'name' => new StringType(),
                            ]
                        ]),
                        'args'    => [
                            'name'          => new StringType(),
                            'related_beans' => new ListType(new ObjectType([
                                'name'   => 'RelatedBeanInputType',
                                'fields' => [
                                    'id'     => new IntType(),
                                    'module' => new StringType(),
                                ]
                            ]))
                        ],
                        'resolve' => function ($source, $args) {
                            return [
                                'id' => '1',
                                'name' => sprintf('Meeting with %d beans', count($args['related_beans'])),
                            ];
                        }
                    ]
                ]
            ])
        ]);
        $processor = new Processor($schema);
        $response  = $processor->processPayload('
mutation ($related_beans: RelatedBeanInputType) {
  createMeeting(name: "Meeting 1", related_beans: $related_beans) {
    id,
    name
  }
}',
            [
                "related_beans" => [
                    ["module" => "contacts", "id" => "5cc6be5b-fb86-2671-e2c0-55e749882d29"],
                    ["module" => "contacts", "id" => "2a135003-3765-af3f-bc54-55e7497e77aa"],

                ]
            ])->getResponseData();
        $this->assertEquals(['data' => ['createMeeting' => [
            'id' => '1',
            'name' => 'Meeting with 2 beans'
        ]]], $response);
    }
}