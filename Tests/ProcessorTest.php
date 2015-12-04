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
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;
use Youshido\Tests\DataProvider\UserType;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param $query
     * @param $response
     *
     * @dataProvider predefinedSchemaProvider
     */
    public function testPredefinedQueries($query, $response)
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
                    'fields'  => [
                        'id'   => ['type' => 'int'],
                        'name' => ['type' => 'string']
                    ],
                    'resolve' => function () {
                        return [
                            'id'   => 1,
                            'name' => 'Alex'
                        ];
                    }
                ]));

        $validator = new ResolveValidator();
        $processor = new Processor($validator);

        $processor->setSchema($schema);

        $processor->processQuery($query);

        $this->assertEquals($processor->getResponseData(), $response);
    }


    public function predefinedSchemaProvider()
    {
        return [
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
                            'possibleTypes' => [],
                            'inputFields'   => [],
                            'ofType'        => []
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
                        'id'   => ['type' => 'int'],
                        'name' => ['type' => 'string']
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
        $processor->processQuery($query);

        $this->assertEquals(
            $processor->getResponseData(),
            $response
        );
    }

    public function schemaProvider()
    {
        return [
            [
                '{ latest { name } }',
                [
                    'data' => ['latest' => null]
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
