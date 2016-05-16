<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/15/16 5:09 PM
*/

namespace Youshido\Tests\Library\Validator;


use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Literal;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Ast\Field as AstField;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Request;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;
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

        $fragment = new Fragment('name', 'User', []);
        $validator->assertValidFragmentForField($fragment, $fieldName, $userType);
    }

    public function testValidateValue()
    {
        $validator = new ResolveValidator();
        $validator->validateResolvedValue('string value', new StringType());
        $this->assertFalse($validator->hasErrors());

        $validator->validateResolvedValue(null, new NonNullType(new StringType()));
        $this->assertTrue($validator->hasErrors());

        $validator->clearErrors();
        $validator->validateResolvedValue('some data', new NonNullType(new StringType()));
        $this->assertFalse($validator->hasErrors());

        $validator->validateResolvedValue('NEW', new TestEnumType());
        $this->assertFalse($validator->hasErrors());

        $validator->validateResolvedValue(1, new TestEnumType());
        $this->assertFalse($validator->hasErrors());

        $validator->validateResolvedValue(2, new TestEnumType());
        $this->assertTrue($validator->hasErrors());
    }

    /**
     * @expectedException \Youshido\GraphQL\Validator\Exception\ResolveException
     */
    public function testInvalidInterfaceImplementation()
    {
        $userType  = new ObjectType([
            'name'   => 'User',
            'fields' => [
                'name' => new StringType(),
            ],
        ]);
        $fieldName = new AstField('name');

        $validator = new ResolveValidator();
        $validator->assertValidFragmentForField(null, $fieldName, $userType);
    }

    /**
     * @expectedException \Youshido\GraphQL\Validator\Exception\ResolveException
     */
    public function testInvalidFragmentNull()
    {
        $userType  = new ObjectType([
            'name'   => 'User',
            'fields' => [
                'name' => new StringType(),
            ],
        ]);
        $fieldName = new AstField('name');
        $fragment  = new Fragment('name', 'Product', []);

        $validator = new ResolveValidator();
        $validator->assertValidFragmentForField($fragment, $fieldName, $userType);
    }

    /**
     * @expectedException \Youshido\GraphQL\Validator\Exception\ResolveException
     */
    public function testInvalidFragmentModel()
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


        $validQuery               = new Query('hero', null, [
            new Argument('planet', new Literal('earth'))
        ]);
        $invalidArgumentQuery     = new Query('hero', null, [
            new Argument('planets', new Literal('earth'))
        ]);
        $invalidArgumentTypeQuery = new Query('hero', null, [
            new Argument('year', new Literal('invalid type'))
        ]);
        $argumentWithVariable     = new Query('hero', null, [
            new Argument('year', $variable)
        ]);
        $argumentWithVariableWrongType     = new Query('hero', null, [
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
