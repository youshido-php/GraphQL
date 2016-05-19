<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11:02 PM 5/13/16
 */

namespace Youshido\Tests\Schema;


use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Union\UnionType;
use Youshido\Tests\DataProvider\TestEmptySchema;
use Youshido\Tests\DataProvider\TestEnumType;
use Youshido\Tests\DataProvider\TestInterfaceType;
use Youshido\Tests\DataProvider\TestObjectType;
use Youshido\Tests\DataProvider\TestSchema;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{

    private $_counter = 0;

    public function testInit()
    {
        $processor = new Processor(new TestEmptySchema());

        $this->assertTrue($processor->hasErrors());
        $this->assertEquals(['errors' => [['message' => 'Schema has to have fields']]], $processor->getResponseData());
    }

    public function testEmptyQueries()
    {
        $processor = new Processor(new TestSchema());

        $error = new \Exception('Invalid request');
        $processor->addError($error);
        $this->assertEquals([$error], $processor->getErrors());
        $this->assertEquals(['errors' => [
            ['message' => 'Invalid request']]
        ], $processor->getResponseData());
    }

    public function testSchemaOperations()
    {
        $schema    = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'me'                => [
                        'type'    => new ObjectType([
                            'name'   => 'User',
                            'fields' => [
                                'firstName' => [
                                    'type'    => new StringType(),
                                    'args'    => [
                                        'shorten' => new BooleanType()
                                    ],
                                    'resolve' => function ($value, $args = []) {
                                        return empty($args['shorten']) ? $value['firstName'] : $value['firstName'];
                                    }
                                ],
                                'lastName'  => new StringType(),
                                'code'      => new IntType(),
                            ]
                        ]),
                        'resolve' => function ($value, $args) {
                            $data = ['firstName' => 'John', 'code' => '007'];
                            if (!empty($args['upper'])) {
                                foreach ($data as $key => $value) {
                                    $data[$key] = strtoupper($value);
                                }
                            }

                            return $data;
                        },
                        'args'    => [
                            'upper' => [
                                'type'    => new BooleanType(),
                                'default' => false
                            ]
                        ]
                    ],
                    'randomUser'        => [
                        'type'    => new TestObjectType(),
                        'resolve' => function () {
                            return ['invalidField' => 'John'];
                        }
                    ],
                    'invalidValueQuery' => [
                        'type'    => new TestObjectType(),
                        'resolve' => function () {
                            return 'stringValue';
                        }
                    ],
                ],
            ])
        ]);
        $processor = new Processor($schema);

        $processor->processPayload('{ me { firstName } }');
        $this->assertEquals(['data' => ['me' => ['firstName' => 'John']]], $processor->getResponseData());

        $processor->processPayload('{ me { firstName, lastName } }');
        $this->assertEquals(['data' => ['me' => ['firstName' => 'John', 'lastName' => null]]], $processor->getResponseData());

        $processor->processPayload('{ me { code } }');
        $this->assertEquals(['data' => ['me' => ['code' => 7]]], $processor->getResponseData());

        $processor->processPayload('{ me(upper:true) { firstName } }');
        $this->assertEquals(['data' => ['me' => ['firstName' => 'JOHN']]], $processor->getResponseData());

        $schema->getMutationType()
            ->addField(new Field([
                'name'    => 'increaseCounter',
                'type'    => new IntType(),
                'resolve' => function ($value, $args, ResolveInfo $info) {
                    return $this->_counter += $args['amount'];
                },
                'args'    => [
                    'amount' => [
                        'type'    => new IntType(),
                        'default' => 1
                    ]
                ]
            ]))->addField(new Field([
                'name'    => 'invalidResolveTypeMutation',
                'type'    => new NonNullType(new IntType()),
                'resolve' => function ($value, $args, $type) {
                    return null;
                }
            ]))->addField(new Field([
                'name'    => 'interfacedMutation',
                'type'    => new TestInterfaceType(),
                'resolve' => function () {
                    return ['name' => 'John'];
                }
            ]));
        $processor->processPayload('mutation { increaseCounter }');
        $this->assertEquals(['data' => ['increaseCounter' => 1]], $processor->getResponseData());

        $processor->processPayload('mutation { invalidMutation }');
        $this->assertEquals(['errors' => [['message' => 'Field "invalidMutation" not found in type "RootSchemaMutation"']]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('mutation { increaseCounter(noArg: 2) }');
        $this->assertEquals(['errors' => [['message' => 'Unknown argument "noArg" on field "increaseCounter"']]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('mutation { increaseCounter(amount: 2) { invalidProp } }');
        $this->assertEquals(['errors' => [['message' => 'Field "invalidProp" not found in type "Int"']], 'data' => ['increaseCounter' => null]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('mutation { increaseCounter(amount: 2) }');
        $this->assertEquals(['data' => ['increaseCounter' => 5]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('{ invalidQuery }');
        $this->assertEquals(['errors' => [['message' => 'Field "invalidQuery" not found in type "RootQuery"']]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('{ invalidValueQuery { id } }');
        $this->assertEquals(['errors' => [['message' => 'Not valid resolved value for "TestObject" type']], 'data' => ['invalidValueQuery' => null]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('{ me { firstName(shorten: true), middle }}');
        $this->assertEquals(['errors' => [['message' => 'Field "middle" not found in type "User"']], 'data' => ['me' => null]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('{ randomUser { region }}');
        $this->assertEquals(['errors' => [['message' => 'Property "region" not found in resolve result']]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('mutation { invalidResolveTypeMutation }');
        $this->assertEquals(['errors' => [['message' => 'Not valid resolved value for "NON_NULL" type']], 'data' => ['invalidResolveTypeMutation' => null]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('mutation { user:interfacedMutation { name }  }');
        $this->assertEquals(['data' => ['user' => ['name' => 'John']]], $processor->getResponseData());
    }

    public function testListEnumsSchemaOperations()
    {
        $processor = new Processor(new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'listQuery'                 => [
                        'type'    => new ListType(new TestEnumType()),
                        'resolve' => function () {
                            return 'invalid list';
                        }
                    ],
                    'listEnumQuery'             => [
                        'type'    => new ListType(new TestEnumType()),
                        'resolve' => function () {
                            return ['invalid enum'];
                        }
                    ],
                    'invalidEnumQuery'          => [
                        'type'    => new TestEnumType(),
                        'resolve' => function () {
                            return 'invalid enum';
                        }
                    ],
                    'enumQuery'                 => [
                        'type'    => new TestEnumType(),
                        'resolve' => function () {
                            return 1;
                        }
                    ],
                    'invalidNonNullQuery'       => [
                        'type'    => new NonNullType(new IntType()),
                        'resolve' => function () {
                            return null;
                        }
                    ],
                    'invalidNonNullInsideQuery' => [
                        'type'    => new NonNullType(new IntType()),
                        'resolve' => function () {
                            return 'hello';
                        }
                    ],
                    'objectQuery'               => [
                        'type'    => new TestObjectType(),
                        'resolve' => function () {
                            return ['name' => 'John'];
                        }
                    ],
                    'deepObjectQuery'           => [
                        'type'    => new ObjectType([
                            'name'   => 'deepObject',
                            'fields' => [
                                'object' => new TestObjectType(),
                                'enum'   => new TestEnumType(),
                            ],
                        ]),
                        'resolve' => function () {
                            return [
                                'object' => [
                                    'name' => 'John'
                                ],
                                'enum'   => 1
                            ];
                        },
                    ],
                ]
            ])
        ]));

        $processor->processPayload('{ listQuery }');
        $this->assertEquals(['errors' => [
            ['message' => 'Not valid resolved value for "LIST" type']
        ], 'data'                     => ['listQuery' => null]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('{ listEnumQuery }');
        $this->assertEquals(['errors' => [
            ['message' => 'Not valid resolve value in listEnumQuery field']
        ], 'data'                     => ['listEnumQuery' => null]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('{ invalidEnumQuery }');
        $this->assertEquals(['errors' => [
            ['message' => 'Not valid resolved value for "TestEnum" type']
        ], 'data'                     => ['invalidEnumQuery' => null]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('{ enumQuery }');
        $this->assertEquals(['data' => ['enumQuery' => 'FINISHED']], $processor->getResponseData());

        $processor->processPayload('{ invalidNonNullQuery }');
        $this->assertEquals(['errors' => [
            ['message' => 'Cannot return null for non-nullable field invalidNonNullQuery.invalidNonNullQuery']
        ], 'data'                     => ['invalidNonNullQuery' => null]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('{ invalidNonNullInsideQuery }');
        $this->assertEquals(['errors' => [
            ['message' => 'Not valid value for SCALAR field invalidNonNullInsideQuery']
        ], 'data'                     => ['invalidNonNullInsideQuery' => null]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('{ test:deepObjectQuery { object { name } } }');
        $this->assertEquals(['data' => ['test' => ['object' => ['name' => 'John']]]], $processor->getResponseData());
    }

    public function testTypedFragment()
    {

        $object1   = new ObjectType([
            'name'   => 'Object1',
            'fields' => [
                'id' => ['type' => 'int']
            ]
        ]);

        $object2 = new ObjectType([
            'name'   => 'Object2',
            'fields' => [
                'name' => ['type' => 'string']
            ]
        ]);

        $union = new UnionType([
            'name'        => 'TestUnion',
            'types'       => [$object1, $object2],
            'resolveType' => function ($object) use ($object1, $object2) {
                if (isset($object['id'])) {
                    return $object1;
                }

                return $object2;
            }
        ]);
        $processor = new Processor(new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'union' => [
                        'type'    => $union,
                        'args'    => [
                            'type' => ['type' => 'string']
                        ],
                        'resolve' => function ($value, $args) {
                            if ($args['type'] == 'object1') {
                                return [
                                    'id' => 43
                                ];
                            } else {
                                return [
                                    'name' => 'name resolved'
                                ];
                            }
                        }
                    ]
                ]
            ])
        ]));
        $processor->processPayload('{ union(type: "object1") { ... on Object2 { id } } }');
        $this->assertEquals(['data' => ['union' => []]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('{ union(type: "object1") { ... on Object1 { name } } }');
        $this->assertEquals([
            'data' => [
                'union' => []
            ],
            'errors' => [
                ['message' => 'Field "name" not found in type "Object1"']
            ]
        ], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('{ union(type: "object1") { ... on Object1 { id } } }');
        $this->assertEquals(['data' => ['union' => ['id' => 43]]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processPayload('{ union(type: "asd") { ... on Object2 { name } } }');
        $this->assertEquals(['data' => ['union' => ['name' => 'name resolved']]], $processor->getResponseData());
        $processor->clearErrors();
    }


}
