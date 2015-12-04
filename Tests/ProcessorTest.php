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
                    'data'   => ['latest' => null],
                    'errors' => ['Not valid resolved value for query "latest"']
                ]
            ],
            [
                '{ user(id:1) { id, name } }',
                [
                    'errors' => ['Not valid type for argument "id" in query "user"']
                ]
            ]
        ];
    }

}
