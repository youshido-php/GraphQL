<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/12/16 7:14 PM
*/

namespace Youshido\Tests\Library\Field;


use Youshido\GraphQL\Execution\ExecutionContext;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Parser\Ast\Field as FieldAST;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\Tests\DataProvider\TestField;
use Youshido\Tests\DataProvider\TestSchema;

class FieldTest extends \PHPUnit_Framework_TestCase
{

    public function testInlineFieldCreation()
    {
        $field = new Field([
            'name' => 'id',
            'type' => new IdType()
        ]);
        $resolveInfo = new ResolveInfo($field, new FieldAST('id'), $field->getType(), new ExecutionContext(new TestSchema()));
        $this->assertEquals('id', $field->getName());
        $this->assertEquals(new IdType(), $field->getType());
        $this->assertEquals(null, $field->resolve('data', null, null));

        $fieldWithResolve = new Field([
            'name'    => 'title',
            'type'    => new StringType(),
            'resolve' => function ($value, $args, ResolveInfo $info) {
                return $info->getReturnType()->serialize($value);
            }
        ]);

        $this->assertEquals('true', $fieldWithResolve->resolve(true, [], $resolveInfo), 'Resolve bool to string');
        $this->assertEquals('CTO', $fieldWithResolve->resolve('CTO', [], $resolveInfo));

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
            'id'   => new InputField(['name' => 'id', 'type' => new IntType()]),
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
