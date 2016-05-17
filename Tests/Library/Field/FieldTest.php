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
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\Tests\DataProvider\TestField;

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
        $this->assertEquals(null, $field->resolve('data', null, null));

        $fieldWithResolve = new Field([
            'name'    => 'title',
            'type'    => new StringType(),
            'resolve' => function ($value, $args, AbstractType $type) {
                return $type->serialize($value);
            }
        ]);

        $this->assertEquals('true', $fieldWithResolve->resolve(true), 'Resolve bool to string');
        $this->assertEquals('CTO', $fieldWithResolve->resolve('CTO'));

        $fieldWithResolve->setType(new IntType());
        $this->assertEquals(new IntType(), $fieldWithResolve->getType());

    }

    public function testObjectFieldCreation()
    {
        $field = new TestField();

        $this->assertEquals('test', $field->getName());
        $this->assertEquals('description', $field->getDescription());
        $this->assertEquals(new IntType(), $field->getType());
        $this->assertEquals('test', $field->resolve('test'));
    }
    public function testArgumentsTrait()
    {
        $testField = new TestField();
        $this->assertFalse($testField->hasArguments());

        $testField->addArgument(new InputField(['name' => 'id', 'type' => new IntType()]));
        $this->assertEquals([
            'id' => new InputField(['name' => 'id', 'type' => new IntType()])
        ], $testField->getArguments());

        $testField->addArguments([
            new InputField(['name' => 'name', 'type' => new StringType()])
        ]);
        $this->assertEquals([
            'id' => new InputField(['name' => 'id', 'type' => new IntType()]),
            'name' => new InputField(['name' => 'name', 'type' => new StringType()]),
        ], $testField->getArguments());

        $testField->removeArgument('name');
        $this->assertFalse($testField->hasArgument('name'));
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

}
