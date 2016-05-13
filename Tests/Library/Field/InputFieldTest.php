<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 2:29 PM 5/13/16
 */

namespace Youshido\Tests\Library\Field;


use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\Tests\DataProvider\TestInputField;

class InputFieldTest extends \PHPUnit_Framework_TestCase
{

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
