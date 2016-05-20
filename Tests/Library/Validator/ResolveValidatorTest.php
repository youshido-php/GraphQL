<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/15/16 5:09 PM
*/

namespace Youshido\Tests\Library\Validator;


use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Literal;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Ast\Field as AstField;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Query;
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

class ResolveValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testValidMethods()
    {
        $validator = new ResolveValidator();
        $this->assertEmpty($validator->getErrors());

        $object       = new TestObjectType();
        $fieldName    = new AstField('name');
        $fieldSummary = new AstField('summary');
        $this->assertTrue($validator->objectHasField($object, $fieldName));
        $this->assertEmpty($validator->getErrors());

        $this->assertFalse($validator->objectHasField($object, $fieldSummary));
        $this->assertNotEmpty($validator->getErrors());

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

    public function testValidateValue()
    {
        $validator = new ResolveValidator();
        $validator->validateResolvedValueType('string value', new StringType());
        $this->assertFalse($validator->hasErrors());

        $validator->validateResolvedValueType(null, new NonNullType(new StringType()));
        $this->assertTrue($validator->hasErrors());

        $validator->clearErrors();
        $validator->validateResolvedValueType('some data', new NonNullType(new StringType()));
        $this->assertFalse($validator->hasErrors());

        $validator->validateResolvedValueType('NEW', new TestEnumType());
        $this->assertFalse($validator->hasErrors());

        $validator->validateResolvedValueType(1, new TestEnumType());
        $this->assertFalse($validator->hasErrors());

        $validator->validateResolvedValueType(2, new TestEnumType());
        $this->assertTrue($validator->hasErrors());
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

        $validator = new ResolveValidator();
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

        $validator = new ResolveValidator();
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
            'resolveType' => function ($object) {
                return new BooleanType();
            }
        ]);

        $validator = new ResolveValidator();
        $validator->assertTypeInUnionTypes($union->resolveType(true), $union);
    }

    public function testValidUnionTypeResolve()
    {
        $union = new UnionType([
            'name'        => 'TestUnion',
            'types'       => [new TestObjectType()],
            'resolveType' => function ($object) {
                return new TestObjectType();
            }
        ]);

        $validator = new ResolveValidator();
        try {
            $validator->assertTypeInUnionTypes($union->resolveType(true), $union);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }
    }

    public function testArgumentsValidation()
    {
        $variable          = new Variable('year', 'Int');
        $variableWrongType = new Variable('year', 'String');
        $variable->setValue(2016);


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
        $validator = new ResolveValidator();
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
            new Argument('year', $variable)
        ]);
        $argumentWithVariableWrongType = new Query('hero', null, [
            new Argument('year', $variableWrongType)
        ]);


        $this->assertFalse($validator->hasErrors());

        $validator->validateArguments($field, $validQuery, $request);
        $this->assertFalse($validator->hasErrors());

        $validator->validateArguments($field, $invalidArgumentQuery, $request);
        $this->assertEquals([
            ['message' => 'Unknown argument "planets" on field "hero"']
        ], $validator->getErrorsArray());
        $validator->clearErrors();

        $validator->validateArguments($field, $invalidArgumentTypeQuery, $request);
        $this->assertEquals([
            ['message' => 'Not valid type for argument "year" in query "hero"']
        ], $validator->getErrorsArray());
        $validator->clearErrors();

        $validator->validateArguments($field, $argumentWithVariable, $request);
        $this->assertEquals([
            ['message' => 'Variable "year" does not exist for query "hero"']
        ], $validator->getErrorsArray());
        $validator->clearErrors();

        $request->setVariables(['year' => '2016']);
        $validator->validateArguments($field, $argumentWithVariable, $request);
        $this->assertFalse($validator->hasErrors());

        $validator->validateArguments($field, $argumentWithVariableWrongType, $request);
        $this->assertEquals([
            ['message' => 'Invalid variable "year" type, allowed type is "Int"']
        ], $validator->getErrorsArray());
        $validator->clearErrors();


    }
}
