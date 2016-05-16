<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 11:02 PM 5/13/16
 */

namespace Youshido\Tests\Schema;


use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{

    private $_counter = 0;

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
                'name'   => 'RootQuery',
                'fields' => [
                    'me' => [
                        'type'    => new ObjectType([
                            'name'   => 'User',
                            'fields' => [
                                'firstName' => [
                                    'type' => new StringType(),
                                    'args' => [
                                        'shorten' => new BooleanType()
                                    ],
                                    'resolve' => function($value, $args = []) {
                                        return empty($args['shorten']) ? $value : $value[0];
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
                ],
            ])
        ]);
        $processor->setSchema($schema);

        $processor->processRequest('{ me { firstName } }');
        $this->assertEquals(['data' => ['me' => ['firstName' => 'John']]], $processor->getResponseData());

        $processor->processRequest('{ me { firstName, lastName } }');
        $this->assertEquals(['data' => ['me' => ['firstName' => 'John', 'lastName' => null]]], $processor->getResponseData());

        $processor->processRequest('{ me { code } }');
        $this->assertEquals(['data' => ['me' => ['code' => 7]]], $processor->getResponseData());

        $processor->processRequest('{ me(upper:true) { firstName } }');
        $this->assertEquals(['data' => ['me' => ['firstName' => 'JOHN']]], $processor->getResponseData());

        $schema->addMutationField(new Field([
            'name'    => 'increaseCounter',
            'type'    => new IntType(),
            'resolve' => function ($value, $args, IntType $type) {
                return $this->_counter += $args['amount'];
            },
            'args'    => [
                'amount' => [
                    'type'    => new IntType(),
                    'default' => 1
                ]
            ]
        ]));
        $processor->processRequest('mutation { increaseCounter }');
        $this->assertEquals(['data' => ['increaseCounter' => 1]], $processor->getResponseData());

        $processor->processRequest('mutation { invalidMutation }');
        $this->assertEquals(['errors' => [['message' => 'Field "invalidMutation" not found in type "RootSchemaMutation"']]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processRequest('mutation { increaseCounter(noArg: 2) }');
        $this->assertEquals(['errors' => [['message' => 'Unknown argument "noArg" on field "increaseCounter"']]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processRequest('mutation { increaseCounter(amount: 2) { invalidProp } }');
        $this->assertEquals(['errors' => [['message' => 'Field "invalidProp" not found in type "Int"']], 'data' => ['increaseCounter' => null]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processRequest('mutation { increaseCounter(amount: 2) }');
        $this->assertEquals(['data' => ['increaseCounter' => 5]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processRequest('{ invalidQuery }');
        $this->assertEquals(['errors' => [['message' => 'Field "invalidQuery" not found in type "RootQuery"']]], $processor->getResponseData());
        $processor->clearErrors();

        $processor->processRequest('{ me { firstName(shorten: true), middle }}');
        $this->assertEquals(['errors' => [['message' => 'Field "middle" not found in type "User"']], 'data' => [ 'me' => null]], $processor->getResponseData());
        $processor->clearErrors();

    }


}
