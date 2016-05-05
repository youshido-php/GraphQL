<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 2:02 AM
*/

namespace Youshido\Tests;

use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;
use Youshido\Tests\DataProvider\UserType;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{

    public function testGoogleExtensionQuery()
    {
        $processor = new Processor();
        $processor->setSchema(new Schema());

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
        $schema = new Schema([
            'query' => new ObjectType([
                'name'        => 'TestSchema',
                'description' => 'Root of TestSchema'
            ])
        ]);
        $schema->addQuery('latest',
            new ObjectType(
                [
                    'name'    => 'latest',
                    'args'    => [
                        'id' => ['type' => TypeMap::TYPE_INT]
                    ],
                    'fields'  => [
                        'id'   => ['type' => TypeMap::TYPE_INT],
                        'name' => ['type' => TypeMap::TYPE_STRING]
                    ],
                    'resolve' => function () {
                        return [
                            'id'   => 1,
                            'name' => 'Alex'
                        ];
                    }
                ]),
            [
                'description'       => 'latest description',
                'deprecationReason' => 'for test',
                'isDeprecated'      => true,
            ]
        );

        $processor = new Processor();

        $processor->setSchema($schema);

        $processor->processRequest($query);
        $responseData = $processor->getResponseData();

        $this->assertEquals($responseData, $expectedResponse);
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
                                ['name' => 'TestSchema', 'fields' => [['name' => 'latest']]],
                                ['name' => 'latest', 'fields' => [['name' => 'id'], ['name' => 'name']]],
                                ['name' => 'Int', 'fields' => null],
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
                            'name'   => 'TestSchema',
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
                            'name'          => 'TestSchema',
                            'description'   => 'Root of TestSchema',
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


    /**
     * @dataProvider schemaProvider
     */
    public function testProcessor($query, $response)
    {
        $schema = new Schema();
        $schema->addQuery('latest',
            new ObjectType(
                [
                    'name'    => 'latest',
                    'fields'  => [
                        'id'   => ['type' => TypeMap::TYPE_INT],
                        'name' => ['type' => TypeMap::TYPE_STRING]
                    ],
                    'resolve' => function () {
                        return [
                            'id'   => 1,
                            'name' => 'Alex'
                        ];
                    }
                ]));

        $schema->addQuery('user', new UserType());

        $validator = new ResolveValidator();
        $processor = new Processor($validator);

        $processor->setSchema($schema);
        $processor->processRequest($query);

        $responseData = $processor->getResponseData();

        $this->assertEquals(
            $responseData,
            $response
        );
    }

    public function schemaProvider()
    {
        return [
            [
                '{ latest { name } }',
                [
                    'data' => ['latest' => ['name' => 'Alex']]
                ]
            ],
            [
                '{ user(id:1) { id, name } }',
                [
                    'data' => ['user' => ['id' => 1, 'name' => 'John']]
                ]
            ]
        ];
    }

}
