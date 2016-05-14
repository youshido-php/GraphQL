<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11:02 PM 5/13/16
 */

namespace Youshido\Tests\Schema;


use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $processor = new Processor();

        $this->assertFalse($processor->hasErrors());
        $this->assertNull($processor->getSchema());
        $this->assertEmpty($processor->getResponseData());
    }

    public function testEmptyQueries()
    {
        $processor = new Processor();

        $error = new \Exception('Invalid request');
        $processor->addError($error);
        $this->assertEquals([$error], $processor->getErrors());
        $this->assertEquals(['errors' => [
            ['message' => 'Invalid request']]
        ], $processor->getResponseData());

        $processor->processRequest('request with no schema');
        $this->assertEquals(['errors' => [
            ['message' => 'You have to set GraphQL Schema to process']]
        ], $processor->getResponseData());
    }

    public function testSchemaOperations()
    {
        $processor = new Processor();

        $schema = new Schema([
            'query' => new ObjectType([
                'name' => 'RootQuery',
                'fields' => [
                    'me' => [
                        'type' => new ObjectType([
                            'name' => 'User',
                            'fields' => [
                                'firstName' => new StringType(),
                                'lastName' => new StringType(),
                            ]
                        ]),
                        'resolve' => function() {
                            return ['firstName' => 'John'];
                        }
                    ],
                ],
            ])
        ]);
        $processor->setSchema($schema);
    }

}
