<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/15/16 7:52 AM
*/

namespace Youshido\Tests\Schema;


use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Type\Enum\EnumType;
use Youshido\GraphQL\Type\InterfaceType\InterfaceType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\Union\UnionType;
use Youshido\Tests\DataProvider\TestEmptySchema;

class IntrospectionTest extends \PHPUnit_Framework_TestCase
{
    private $introspectionQuery = <<<TEXT
query IntrospectionQuery {
                __schema {
                    queryType { name }
                    mutationType { name }
                    types {
                        ...FullType
                    }
                    directives {
                        name
                        description
                        args {
                            ...InputValue
                        }
                        onOperation
                        onFragment
                        onField
                    }
                }
            }

            fragment FullType on __Type {
                kind
                name
                description
                fields {
                    name
                    description
                    args {
                        ...InputValue
                    }
                    type {
                        ...TypeRef
                    }
                    isDeprecated
                    deprecationReason
                }
                inputFields {
                    ...InputValue
                }
                interfaces {
                    ...TypeRef
                }
                enumValues {
                    name
                    description
                    isDeprecated
                    deprecationReason
                }
                possibleTypes {
                    ...TypeRef
                }
            }

            fragment InputValue on __InputValue {
                name
                description
                type { ...TypeRef }
                defaultValue
            }

            fragment TypeRef on __Type {
                kind
                name
                ofType {
                    kind
                    name
                    ofType {
                        kind
                        name
                        ofType {
                            kind
                            name
                        }
                    }
                }
            }
TEXT;


    public function testIntrospectionDirectiveRequest()
    {
        $processor = new Processor(new TestEmptySchema());

        $processor->processPayload($this->introspectionQuery, []);

        $this->assertTrue(is_array($processor->getResponseData()));
    }

    /**
     * @param $query
     * @param $expectedResponse
     *
     * @dataProvider predefinedSchemaProvider
     */
    public function testPredefinedQueries($query, $expectedResponse)
    {
        $schema = new TestEmptySchema();
        $schema->addQueryField(new Field([
            'name'              => 'latest',
            'type'              => new ObjectType([
                'name'   => 'LatestType',
                'fields' => [
                    'id'   => ['type' => TypeMap::TYPE_INT],
                    'name' => ['type' => TypeMap::TYPE_STRING]
                ],
            ]),
            'args'              => [
                'id' => ['type' => TypeMap::TYPE_INT]
            ],
            'description'       => 'latest description',
            'deprecationReason' => 'for test',
            'isDeprecated'      => true,
            'resolve'           => function () {
                return [
                    'id'   => 1,
                    'name' => 'Alex'
                ];
            }
        ]));

        $processor = new Processor($schema);

        $processor->processPayload($query);
        $responseData = $processor->getResponseData();

        $this->assertEquals($expectedResponse, $responseData);
    }

    public function predefinedSchemaProvider()
    {
        return [
            [
                '{ __type { name } }',
                [
                    'errors' => [['message' => 'Require "name" arguments to query "__type"']]
                ]
            ],
            [
                '{ __type (name: "__Type") { name } }',
                [
                    'data' => [
                        '__type' => ['name' => '__Type']
                    ]
                ]
            ],
            [
                '{ __type (name: "NoExist") { name } }',
                [
                    'data' => [
                        '__type' => null
                    ]
                ]
            ],
            [
                '{
                    __schema {
                        types {
                            name,
                            fields {
                                name
                            }
                        }
                    }
                }',
                [
                    'data' => [
                        '__schema' => [
                            'types' => [
                                ['name' => 'TestSchemaQuery', 'fields' => [['name' => 'latest']]],
                                ['name' => 'Int', 'fields' => null],
                                ['name' => 'LatestType', 'fields' => [['name' => 'id'], ['name' => 'name']]],
                                ['name' => 'String', 'fields' => null],
                                ['name' => '__Schema', 'fields' => [['name' => 'queryType'], ['name' => 'mutationType'], ['name' => 'subscriptionType'], ['name' => 'types'], ['name' => 'directives']]],
                                ['name' => '__Type', 'fields' => [['name' => 'name'], ['name' => 'kind'], ['name' => 'description'], ['name' => 'ofType'], ['name' => 'inputFields'], ['name' => 'enumValues'], ['name' => 'fields'], ['name' => 'interfaces'], ['name' => 'possibleTypes']]],
                                ['name' => '__InputValue', 'fields' => [['name' => 'name'], ['name' => 'description'], ['name' => 'type'], ['name' => 'defaultValue'],]],
                                ['name' => '__EnumValue', 'fields' => [['name' => 'name'], ['name' => 'description'], ['name' => 'deprecationReason'], ['name' => 'isDeprecated'],]],
                                ['name' => 'Boolean', 'fields' => null],
                                ['name' => '__Field', 'fields' => [['name' => 'name'], ['name' => 'description'], ['name' => 'isDeprecated'], ['name' => 'deprecationReason'], ['name' => 'type'], ['name' => 'args']]],
                                ['name' => '__Subscription', 'fields' => [['name' => 'name']]],
                                ['name' => '__Directive', 'fields' => [['name' => 'name'], ['name' => 'description'], ['name' => 'args'], ['name' => 'onOperation'], ['name' => 'onFragment'], ['name' => 'onField']]],
                            ]
                        ]
                    ]
                ]
            ],
            [
                '{
                  test : __schema {
                    queryType {
                      kind,
                      name,
                      fields {
                        name,
                        isDeprecated,
                        deprecationReason,
                        description,
                        type {
                          name
                        }
                      }
                    }
                  }
                }',
                ['data' => [
                    'test' => [
                        'queryType' => [
                            'name'   => 'TestSchemaQuery',
                            'kind'   => 'OBJECT',
                            'fields' => [
                                ['name' => 'latest', 'isDeprecated' => true, 'deprecationReason' => 'for test', 'description' => 'latest description', 'type' => ['name' => 'LatestType']]
                            ]
                        ]
                    ]
                ]]
            ],
            [
                '{
                  __schema {
                    queryType {
                      kind,
                      name,
                      description,
                      interfaces {
                        name
                      },
                      possibleTypes {
                        name
                      },
                      inputFields {
                        name
                      },
                      ofType{
                        name
                      }
                    }
                  }
                }',
                ['data' => [
                    '__schema' => [
                        'queryType' => [
                            'kind'          => 'OBJECT',
                            'name'          => 'TestSchemaQuery',
                            'description'   => null,
                            'interfaces'    => [],
                            'possibleTypes' => null,
                            'inputFields'   => null,
                            'ofType'        => null
                        ]
                    ]
                ]]
            ]
        ];
    }

    public function testCombinedFields()
    {
        $schema = new TestEmptySchema();

        $interface = new InterfaceType([
            'name'        => 'TestInterface',
            'fields'      => [
                'id'   => ['type' => new IntType()],
                'name' => ['type' => new IntType()],
            ],
            'resolveType' => function ($type) {

            }
        ]);

        $object1 = new ObjectType([
            'name'       => 'Test1',
            'fields'     => [
                'id'       => ['type' => new IntType()],
                'name'     => ['type' => new IntType()],
                'lastName' => ['type' => new IntType()],
            ],
            'interfaces' => [$interface]
        ]);

        $object2 = new ObjectType([
            'name'       => 'Test2',
            'fields'     => [
                'id'        => ['type' => new IntType()],
                'name'      => ['type' => new IntType()],
                'thirdName' => ['type' => new IntType()],
            ],
            'interfaces' => [$interface]
        ]);

        $unionType = new UnionType([
            'name'        => 'UnionType',
            'types'       => [$object1, $object2],
            'resolveType' => function () {

            }
        ]);

        $schema->addQueryField(new Field([
            'name'    => 'union',
            'type'    => $unionType,
            'args'    => [
                'id' => ['type' => TypeMap::TYPE_INT]
            ],
            'resolve' => function () {
                return [
                    'id'   => 1,
                    'name' => 'Alex'
                ];
            }
        ]));

        $schema->addMutationField(new Field([
            'name'    => 'mutation',
            'type'    => $unionType,
            'args'    => [
                'type' => new EnumType([
                    'name'   => 'MutationType',
                    'values' => [
                        [
                            'name'  => 'Type1',
                            'value' => 'type_1'
                        ],
                        [
                            'name'  => 'Type2',
                            'value' => 'type_2'
                        ]
                    ]
                ])
            ],
            'resolve' => function () {
                return null;
            }
        ]));

        $processor = new Processor($schema);

        $processor->processPayload($this->introspectionQuery);
        $responseData = $processor->getResponseData();

        /** strange that this test got broken after I fixed the field resolve behavior */
        $this->assertArrayNotHasKey('errors', $responseData);
    }

}
