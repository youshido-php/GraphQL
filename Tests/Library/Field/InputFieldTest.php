<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 2:29 PM 5/13/16
 */

namespace Youshido\Tests\Library\Field;


use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\InputObject\InputObjectType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Youshido\Tests\DataProvider\TestInputField;

class InputFieldTest extends \PHPUnit_Framework_TestCase
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

    public function testFieldWithInputFieldArgument()
    {
        $schema    = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'amount' => [
                        'type' => new IntType(),
                        'args' => [
                            new InputField([
                                'name' => 'input',
                                'type' => new InputObjectType([
                                    'name'   => 'TestInput',
                                    'fields' => [
                                        new InputField(['name' => 'clientMutationId', 'type' => new NonNullType(new StringType())])
                                    ]
                                ])
                            ])
                        ],
                    ]

                ]
            ])
        ]);
        $processor = new Processor($schema);
        $processor->processPayload($this->introspectionQuery);
    }

    public function testInlineInputFieldCreation()
    {
        $field = new InputField([
            'name'         => 'id',
            'type'         => 'id',
            'description'  => 'description',
            'defaultValue' => 123
        ]);

        $this->assertEquals('id', $field->getName());
        $this->assertEquals(new IdType(), $field->getType());
        $this->assertEquals('description', $field->getDescription());
        $this->assertSame(123, $field->getDefaultValue());
    }


    public function testObjectInputFieldCreation()
    {
        $field = new TestInputField();

        $this->assertEquals('testInput', $field->getName());
        $this->assertEquals('description', $field->getDescription());
        $this->assertEquals(new IntType(), $field->getType());
        $this->assertEquals('default', $field->getDefaultValue());
    }

    public function testListAsInputField()
    {
        new InputField([
            'name' => 'test',
            'type' => new ListType(new IntType()),
        ]);
    }

    /**
     * @dataProvider invalidInputFieldProvider
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidInputFieldParams($fieldConfig)
    {
        $field = new InputField($fieldConfig);
        ConfigValidator::getInstance()->assertValidConfig($field->getConfig());
    }

    public function invalidInputFieldProvider()
    {
        return [
            [
                [
                    'name' => 'id',
                    'type' => 'invalid type'
                ]
            ],
            [
                [
                    'name' => 'id',
                    'type' => new ObjectType([
                        'name'   => 'test',
                        'fields' => [
                            'id' => ['type' => 'int']
                        ]
                    ])
                ]
            ],
        ];
    }
}
