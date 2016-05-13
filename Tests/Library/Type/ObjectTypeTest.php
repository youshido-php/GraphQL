<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/11/16 9:43 PM
*/

namespace Youshido\Tests\Library;


use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\Tests\DataProvider\TestObjectType;

class ObjectTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testCreatingInvalidObject()
    {
        new ObjectType([]);
    }

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testInvalidNameParam()
    {
        new ObjectType([
            'name' => null
        ]);
    }

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testInvalidFieldsParam()
    {
        new ObjectType([
            'name' => 'SomeName',
            'fields' => []
        ]);
    }

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ResolveException
     */
    public function testSerialize()
    {
        $object = new ObjectType([
            'name' => 'SomeName',
            'fields' => [
                'name' => new StringType()
            ]
        ]);
        $object->serialize([]);
    }


    public function testNormalCreatingParam()
    {
        $objectType = new ObjectType([
            'name' => 'Post',
            'fields' => [
                'id' => new IntType()
            ]
        ]);
        $this->assertEquals($objectType->getKind(), TypeMap::KIND_OBJECT);
        $this->assertEquals($objectType->getName(), 'Post');
        $this->assertEquals($objectType->getType(), $objectType);
        $this->assertEquals($objectType->getType()->getName(), 'Post');
        $this->assertEquals($objectType->getNamedType(), $objectType);

        $this->assertEmpty($objectType->getInterfaces());
        $this->assertTrue($objectType->isValidValue($objectType));
        $this->assertFalse($objectType->isValidValue(null));

        $this->assertNull($objectType->getDescription());
    }

    public function testExtendedClass()
    {
        $objectType = new TestObjectType();
        $this->assertEquals($objectType->getName(), 'TestObject');
        $this->assertEquals($objectType->getField('name')->getKind(), TypeMap::KIND_SCALAR);
        $this->assertEquals($objectType->getType(), $objectType, 'test type of extended object');
    }

}
