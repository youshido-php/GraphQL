<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/15/16 3:28 PM
*/

namespace Youshido\Tests\Library\Type;


use Youshido\GraphQL\Type\InputObject\InputObjectType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\Tests\DataProvider\TestInputObjectType;

class InputObjectTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testInternal()
    {
        $inputObjectType = new InputObjectType([
            'name' => 'PostData',
            'fields' => [
                'title' => new NonNullType(new StringType())
            ]
        ]);
        $this->assertEquals(TypeMap::KIND_INPUT_OBJECT, $inputObjectType->getKind());
        $this->assertEquals('PostData', $inputObjectType->getName());

        $this->assertFalse($inputObjectType->isValidValue('invalid value'));
        $this->assertTrue($inputObjectType->isValidValue(['title' => 'Super ball!']));
        $this->assertFalse($inputObjectType->isValidValue(['title' => null]));
    }

    public function testStandaloneClass()
    {
        $inputObjectType = new TestInputObjectType();
        $this->assertEquals('TestInputObject', $inputObjectType->getName());
    }

}
