<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/15/16 5:09 PM
*/

namespace Youshido\Tests\Library\Validator;


use Youshido\GraphQL\Execution\Context\ExecutionContext;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Literal;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\VariableReference;
use Youshido\GraphQL\Parser\Ast\Field as AstField;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Union\UnionType;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;
use Youshido\Tests\DataProvider\TestEnumType;
use Youshido\Tests\DataProvider\TestInterfaceType;
use Youshido\Tests\DataProvider\TestObjectType;
use Youshido\Tests\DataProvider\TestUnionType;
use Youshido\Tests\DataProvider\TestSchema;

class ResolveValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testValidMethods()
    {
        $validator = new ResolveValidator(new ExecutionContext(new TestSchema()));
        $this->assertEmpty($validator->getExecutionContext()->getErrors());

        $object       = new TestObjectType();
        $fieldName    = new AstField('name');
        $fieldSummary = new AstField('summary');
        $this->assertTrue($validator->objectHasField($object, $fieldName));
        $this->assertEmpty($validator->getExecutionContext()->getErrors());

        $this->assertFalse($validator->objectHasField($object, $fieldSummary));
        $this->assertNotEmpty($validator->getExecutionContext()->getErrors());

        $userType = new ObjectType([
            'name'       => 'User',
            'fields'     => [
                'name' => new StringType(),
            ],
            'interfaces' => [new TestInterfaceType()]
        ]);

        $validator->assertTypeImplementsInterface($userType, new TestInterfaceType());

        $fragment          = new Fragment('name', 'User', []);
        $fragmentReference = new FragmentReference('name');
        $validator->assertValidFragmentForField($fragment, $fragmentReference, $userType);
    }

    public function testValidFragmentTypeWithComposite()
    {
      $validator = new ResolveValidator(new ExecutionContext(new TestSchema()));
      $userType = new ObjectType([
        'name'       => 'User',
        'fields'     => [
          'name' => new StringType(),
        ],
        'interfaces' => [new TestInterfaceType()]
      ]);

      $fragment          = new Fragment('name', 'User', []);
      $fragmentReference = new FragmentReference('name');
      $validator->assertValidFragmentForField($fragment, $fragmentReference, new NonNullType($userType));
    }

    public function testValidFragmentTypeWithUnion()
    {
      $validator = new ResolveValidator(new ExecutionContext(new TestSchema()));
      $unionType = new TestUnionType();

      $fragment          = new Fragment('name', 'TestObject', ['name']);
      $fragmentReference = new FragmentReference('name');
      $validator->assertValidFragmentForField($fragment, $fragmentReference, $unionType);
    }

    public function testValidFragmentTypeWithInterface()
    {
      $validator = new ResolveValidator(new ExecutionContext(new TestSchema()));
      $interfaceType = new TestInterfaceType();

      $fragment          = new Fragment('name', 'TestObject', ['name']);
      $fragmentReference = new FragmentReference('name');
      $validator->assertValidFragmentForField($fragment, $fragmentReference, $interfaceType);
    }

    /**
     * @expectedException \Youshido\GraphQL\Validator\Exception\ResolveException
     */
    public function testInvalidFragmentType()
    {
        $userType          = new ObjectType([
            'name'   => 'User',
            'fields' => [
                'name' => new StringType(),
            ],
        ]);
        $fragmentReference = new FragmentReference('user');
        $fragment          = new Fragment('name', 'Product', []);

        $validator = new ResolveValidator(new ExecutionContext(new TestSchema()));
        $validator->assertValidFragmentForField($fragment, $fragmentReference, $userType);
    }

    /**
     * @expectedException \Youshido\GraphQL\Validator\Exception\ResolveException
     */
    public function testInvalidInterfaceTypeResolve()
    {
        $userType = new ObjectType([
            'name'   => 'User',
            'fields' => [
                'name' => new StringType(),
            ],
        ]);

        $validator = new ResolveValidator(new ExecutionContext(new TestSchema()));
        $validator->assertTypeImplementsInterface($userType, new TestInterfaceType());
    }

    /**
     * @expectedException \Youshido\GraphQL\Validator\Exception\ResolveException
     */
    public function testInvalidUnionTypeResolve()
    {
        $union = new UnionType([
            'name'        => 'TestUnion',
            'types'       => [new TestObjectType()],
            'resolveType' => function () {
                return new BooleanType();
            }
        ]);

        $validator = new ResolveValidator(new ExecutionContext(new TestSchema()));
        $validator->assertTypeInUnionTypes($union->resolveType(new \stdClass()), $union);
    }

    public function testValidUnionTypeResolve()
    {
        $union = new UnionType([
            'name'        => 'TestUnion',
            'types'       => [new TestObjectType()],
            'resolveType' => function () {
                return new TestObjectType();
            }
        ]);

        $validator = new ResolveValidator(new ExecutionContext(new TestSchema()));
        try {
            $validator->assertTypeInUnionTypes($union->resolveType(new \stdClass()), $union);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }
    }

    public function testArgumentsValidation()
    {
        $variable          = new Variable('year', 'Int');
        $variable->setValue(2016);

        $variableWrongType = new Variable('year', 'String');

        $variableReference = new VariableReference('year', $variable);
        $variableWrongTypeReference = new VariableReference('year', $variableWrongType);


        $field     = new Field([
            'name' => 'hero',
            'type' => new ObjectType([
                'name'   => 'User',
                'fields' => [
                    'name' => new StringType()
                ]
            ]),
            'args' => [
                'planet' => new StringType(),
                'year'   => new IntType(),
            ]
        ]);
        $validator = new ResolveValidator(new ExecutionContext(new TestSchema()));
        $request   = new Request([]);


        $validQuery                    = new Query('hero', null, [
            new Argument('planet', new Literal('earth'))
        ]);
        $invalidArgumentQuery          = new Query('hero', null, [
            new Argument('planets', new Literal('earth'))
        ]);
        $invalidArgumentTypeQuery      = new Query('hero', null, [
            new Argument('year', new Literal('invalid type'))
        ]);
        $argumentWithVariable          = new Query('hero', null, [
            new Argument('year', $variableReference)
        ]);
        $argumentWithVariableWrongType = new Query('hero', null, [
            new Argument('year', $variableWrongTypeReference)
        ]);


        $this->assertFalse($validator->getExecutionContext()->hasErrors());

        $validator->validateArguments($field, $validQuery, $request);
        $this->assertFalse($validator->getExecutionContext()->hasErrors());

        $validator->validateArguments($field, $invalidArgumentQuery, $request);
        $this->assertEquals([
            ['message' => 'Unknown argument "planets" on field "hero"']
        ], $validator->getExecutionContext()->getErrorsArray());
        $validator->getExecutionContext()->clearErrors();

        $validator->validateArguments($field, $invalidArgumentTypeQuery, $request);
        $this->assertEquals([
            ['message' => 'Not valid type for argument "year" in query "hero"']
        ], $validator->getExecutionContext()->getErrorsArray());
        $validator->getExecutionContext()->clearErrors();

        $validator->validateArguments($field, $argumentWithVariable, $request);
        $this->assertEquals([
            ['message' => 'Variable "year" does not exist for query "hero"']
        ], $validator->getExecutionContext()->getErrorsArray());
        $validator->getExecutionContext()->clearErrors();

        $request->setVariables(['year' => 2016]);
        $validator->validateArguments($field, $argumentWithVariable, $request);
        $this->assertFalse($validator->getExecutionContext()->hasErrors());

        $request->setVariables(['year' => 0]);
        $validator->validateArguments($field, $argumentWithVariable, $request);
        $this->assertFalse($validator->getExecutionContext()->hasErrors());
        $request->setVariables([]);

        $validator->validateArguments($field, $argumentWithVariableWrongType, $request);
        $this->assertEquals([
            ['message' => 'Invalid variable "year" type, allowed type is "Int"']
        ], $validator->getExecutionContext()->getErrorsArray());
        $validator->getExecutionContext()->clearErrors();


    }
}
