<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11:02 PM 5/13/16
 */

namespace Youshido\Tests\Schema;


use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\Field;
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

    /**
     * @expectedException \Youshido\GraphQL\Validator\Exception\ConfigurationException
     * @expectedExceptionMessage Schema has to have fields
     */
    public function testInit()
    {
        new Processor(new TestEmptySchema());
    }

    public function testEmptyQueries()
    {
        $processor = new Processor(new TestSchema());
        $processor->processPayload('');
        $this->assertEquals(['errors' => [
            ['message' => 'Must provide an operation.']
        ]], $processor->getResponseData());

        $processor->processPayload('{ me { name } }');
        $this->assertEquals(['data' => [
            'me' => ['name' => 'John']
        ]], $processor->getResponseData());

    }

  public function testNestedVariables() {
    $processor = new Processor(new TestSchema());
    $noArgsQuery = '{ me { echo(value:"foo") } }';
    $expectedData = ['data' => ['me' => ['echo' => 'foo']]];
    $processor->processPayload($noArgsQuery, ['value' => 'foo']);
    $this->assertEquals($expectedData, $processor->getResponseData());

    $parameterizedFieldQuery =
        'query nestedFieldQuery($value:String!){
          me {
            echo(value:$value)
          }
        }';
    $processor->processPayload($parameterizedFieldQuery, ['value' => 'foo']);
    $this->assertEquals($expectedData, $processor->getResponseData());

    $parameterizedQueryQuery =
        'query nestedQueryQuery($value:Int){
          me {
            location(noop:$value) {
              address
            }
          }
        }';
    $processor->processPayload($parameterizedQueryQuery, ['value' => 1]);
    $this->assertArrayNotHasKey('errors', $processor->getResponseData());
  }

    public function testListNullResponse()
    {
        $processor = new Processor(new Schema([
            'query' => new ObjectType([
                'name' => 'RootQuery',
                'fields' => [
                    'list' => [
                        'type' => new ListType(new StringType()),
                        'resolve' => function() {
                            return null;
                        }
                    ]
                ]
            ])
        ]));
        $data = $processor->processPayload(' { list }')->getResponseData();
        $this->assertEquals(['data' => ['list' => null]], $data);
    }


    public function testSubscriptionNullResponse()
    {
        $processor = new Processor(new Schema([
            'query' => new ObjectType([
                'name' => 'RootQuery',
                'fields' => [
                    'list' => [
                        'type' => new ListType(new StringType()),
                        'resolve' => function() {
                            return null;
                        }
                    ]
                ]
            ])
        ]));
        $data = $processor->processPayload(' { __schema { subscriptionType { name } } }')->getResponseData();
        $this->assertEquals(['data' => ['__schema' => ['subscriptionType' => null]]], $data);
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
                                    'resolve' => function ($value, $args) {
                                        return empty($args['shorten']) ? $value : $value;
                                    }
                                ],
                                'lastName'  => new StringType(),
                                'code'      => new StringType(),
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
                    'labels' => [
                        'type' => new ListType(new StringType()),
                        'resolve' => function() {
                            return ['one', 'two'];
                        }
                    ]
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

        $processor->processPayload('{ labels }');
        $this->assertEquals(['data' => ['labels' => ['one', 'two']]], $processor->getResponseData());

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
                'resolve' => function () {
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

        $processor->processPayload('mutation { increaseCounter(noArg: 2) }');
        $this->assertEquals(['errors' => [['message' => 'Unknown argument "noArg" on field "increaseCounter"']]], $processor->getResponseData());

        $processor->processPayload('mutation { increaseCounter(amount: 2) { invalidProp } }');
        $this->assertEquals(['errors' => [['message' => 'Field "invalidProp" not found in type "Int"']], 'data' => ['increaseCounter' => null]], $processor->getResponseData());

        $processor->processPayload('mutation { increaseCounter(amount: 2) }');
        $this->assertEquals(['data' => ['increaseCounter' => 5]], $processor->getResponseData());

        $processor->processPayload('{ invalidQuery }');
        $this->assertEquals(['errors' => [['message' => 'Field "invalidQuery" not found in type "RootQuery"']]], $processor->getResponseData());

        $processor->processPayload('{ invalidValueQuery { id } }');
        $this->assertEquals(['errors' => [['message' => 'Not valid value for OBJECT field invalidValueQuery']], 'data' => ['invalidValueQuery' => null]], $processor->getResponseData());

        $processor->processPayload('{ me { firstName(shorten: true), middle }}');
        $this->assertEquals(['errors' => [['message' => 'Field "middle" not found in type "User"']], 'data' => ['me' => null]], $processor->getResponseData());

        $processor->processPayload('{ randomUser { region }}');
        $this->assertEquals(['errors' => [['message' => 'Property "region" not found in resolve result']]], $processor->getResponseData());

        $processor->processPayload('mutation { invalidResolveTypeMutation }');
        $this->assertEquals(['errors' => [['message' => 'Cannot return null for non-nullable field invalidResolveTypeMutation']], 'data' => ['invalidResolveTypeMutation' => null]], $processor->getResponseData());

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
            ['message' => 'Not valid value for LIST field listQuery']
        ], 'data'                     => ['listQuery' => null]], $processor->getResponseData());

        $processor->processPayload('{ listEnumQuery }');
        $this->assertEquals(['errors' => [
            ['message' => 'Not valid resolve value in listEnumQuery field']
        ], 'data'                     => ['listEnumQuery' => [null]]], $processor->getResponseData());

        $processor->processPayload('{ invalidEnumQuery }');
        $this->assertEquals(['errors' => [
            ['message' => 'Not valid value for ENUM field invalidEnumQuery']
        ], 'data'                     => ['invalidEnumQuery' => null]], $processor->getResponseData());

        $processor->processPayload('{ enumQuery }');
        $this->assertEquals(['data' => ['enumQuery' => 'FINISHED']], $processor->getResponseData());

        $processor->processPayload('{ invalidNonNullQuery }');
        $this->assertEquals(['errors' => [
            ['message' => 'Cannot return null for non-nullable field invalidNonNullQuery']
        ], 'data'                     => ['invalidNonNullQuery' => null]], $processor->getResponseData());

        $processor->processPayload('{ invalidNonNullInsideQuery }');
        $this->assertEquals(['errors' => [
            ['message' => 'Not valid value for SCALAR field invalidNonNullInsideQuery']
        ], 'data'                     => ['invalidNonNullInsideQuery' => null]], $processor->getResponseData());

        $processor->processPayload('{ test:deepObjectQuery { object { name } } }');
        $this->assertEquals(['data' => ['test' => ['object' => ['name' => 'John']]]], $processor->getResponseData());
    }

    public function testTypedFragment()
    {

        $object1 = new ObjectType([
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

        $object3 = new ObjectType([
            'name'   => 'Object3',
            'fields' => [
                'name' => ['type' => 'string']
            ]
        ]);

        $union        = new UnionType([
            'name'        => 'TestUnion',
            'types'       => [$object1, $object2],
            'resolveType' => function ($object) use ($object1, $object2) {
                if (isset($object['id'])) {
                    return $object1;
                }

                return $object2;
            }
        ]);
        $invalidUnion = new UnionType([
            'name'        => 'TestUnion',
            'types'       => [$object1, $object2],
            'resolveType' => function ($object) use ($object3) {
                return $object3;
            }
        ]);
        $processor    = new Processor(new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'union'        => [
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
                    ],
                    'invalidUnion' => [
                        'type'    => $invalidUnion,
                        'resolve' => function () {
                            return ['name' => 'name resolved'];
                        }
                    ],
                ]
            ])
        ]));
        $processor->processPayload('{ union(type: "object1") { ... on Object2 { id } } }');
        $this->assertEquals(['data' => ['union' => []]], $processor->getResponseData());

        $processor->processPayload('{ union(type: "object1") { ... on Object1 { name } } }');
        $this->assertEquals([
            'data'   => [
                'union' => []
            ],
            'errors' => [
                ['message' => 'Field "name" not found in type "Object1"']
            ]
        ], $processor->getResponseData());

        $processor->processPayload('{ union(type: "object1") { ... on Object1 { id } } }');
        $this->assertEquals(['data' => ['union' => ['id' => 43]]], $processor->getResponseData());

        $processor->processPayload('{ union(type: "asd") { ... on Object2 { name } } }');
        $this->assertEquals(['data' => ['union' => ['name' => 'name resolved']]], $processor->getResponseData());

        $processor->processPayload('{ invalidUnion { ... on Object2 { name } } }');
        $this->assertEquals(['errors' => [['message' => 'Type Object3 not exist in types of Object2']]], $processor->getResponseData());

    }


}
