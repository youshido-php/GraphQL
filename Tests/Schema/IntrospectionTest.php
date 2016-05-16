<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/15/16 7:52 AM
*/

namespace Youshido\Tests\Schema;


use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\Tests\DataProvider\TestEmptySchema;

class IntrospectionTest extends \PHPUnit_Framework_TestCase
{
    public function testIntrospectionDirectiveRequest()
    {
        $processor = new Processor();
        $processor->setSchema(new TestEmptySchema());

        $processor->processRequest('
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
        ', []);

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
        $schema->addQueryField('latest',
            [
                'type'              => new ObjectType([
                    'name'   => 'latest',

                    'fields' => [
                        'id'   => ['type' => TypeMap::TYPE_INT],
                        'name' => ['type' => TypeMap::TYPE_STRING]
                    ],
                ]),
                'args'   => [
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
            ]
        );

        $processor = new Processor();

        $processor->setSchema($schema);

        $processor->processRequest($query);
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
                                ['name' => 'latest', 'fields' => [['name' => 'id'], ['name' => 'name']]],
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
                                ['name' => 'latest', 'isDeprecated' => true, 'deprecationReason' => 'for test', 'description' => 'latest description', 'type' => ['name' => 'latest']]
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

}
