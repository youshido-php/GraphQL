<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/12/16 7:14 PM
*/

namespace Youshido\Tests\Library\Field;


use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\Tests\DataProvider\TestField;
use Youshido\Tests\DataProvider\TestInputField;

class FieldTest extends \PHPUnit_Framework_TestCase
{

    public function testInlineFieldCreation()
    {
        $field = new Field([
            'name' => 'id',
            'type' => new IdType()
        ]);
        $this->assertEquals('id', $field->getName());
        $this->assertEquals(new IdType(), $field->getType());
        $this->assertNull($field->resolve('data', null, null));

        $fieldWithResolve = new Field([
            'name'    => 'title',
            'type'    => new StringType(),
            'resolve' => function ($value, $args, AbstractType $type) {
                return $type->serialize($value);
            }
        ]);

        $this->assertEquals('true', $fieldWithResolve->resolve(true), 'Resolve bool to string');
        $this->assertEquals('CTO', $fieldWithResolve->resolve('CTO'));
    }

    public function testObjectFieldCreation()
    {
        $field = new TestField();

        $this->assertEquals('Test', $field->getName());
        $this->assertEquals('description', $field->getDescription());
        $this->assertEquals(new IntType(), $field->getType());
        $this->assertEquals('test', $field->resolve('test'));
    }

    public function testInlineInputFieldCreation()
    {
        $field = new InputField([
            'name'        => 'id',
            'type'        => 'id',
            'description' => 'description',
            'default'     => 123
        ]);

        $this->assertEquals('id', $field->getName());
        $this->assertEquals(new IdType(), $field->getType());
        $this->assertEquals('description', $field->getDescription());
        $this->assertEquals(123, $field->getDefaultValue());
    }


    public function testObjectInputFieldCreation()
    {
        $field = new TestInputField();

        $this->assertEquals('TestInput', $field->getName());
        $this->assertEquals('description', $field->getDescription());
        $this->assertEquals(new IntType(), $field->getType());
        $this->assertEquals('default', $field->getDefaultValue());

    }

    /**
     * @param $fieldConfig
     *
     * @dataProvider invalidFieldProvider
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testInvalidFieldParams($fieldConfig)
    {
        new Field($fieldConfig);
    }

    public function invalidFieldProvider()
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
                    'type' => TypeMap::TYPE_FLOAT
                ]
            ]
        ];
    }

    /**
     * @dataProvider invalidInputFieldProvider
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testInvalidInputFieldParams($fieldConfig)
    {
        new InputField($fieldConfig);
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
