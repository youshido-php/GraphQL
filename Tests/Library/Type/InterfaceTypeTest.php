<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/12/16 5:02 PM
*/

namespace Youshido\Tests\Library\Type;


use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\InterfaceType\InterfaceType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\Tests\DataProvider\TestInterfaceType;

class InterfaceTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testInterfaceMethods()
    {
        $interface = new TestInterfaceType();
        $this->assertEquals($interface->getNamedType(), $interface->getType());
        $this->assertEquals(['name' => new Field(['name' => 'name', 'type' => new StringType()])],
            $interface->getFields());

        $object = new ObjectType([
            'name'       => 'Test',
            'fields'     => [
                'name' => new StringType()
            ],
            'interfaces' => [$interface],
        ]);
        $this->assertEquals([$interface], $object->getInterfaces());
        $this->assertTrue($interface->isValidValue($object));
        $this->assertFalse($interface->isValidValue('invalid object'));

        $this->assertEquals($interface->serialize($object), $object);

        $interfaceType = new InterfaceType([
            'name'        => 'UserInterface',
            'fields'      => [
                'name' => new StringType()
            ],
            'resolveType' => function ($object) {
                return $object;
            }
        ]);
        $this->assertEquals('UserInterface', $interfaceType->getName());

        $this->assertEquals($object, $interfaceType->resolveType($object));

        $this->assertFalse($interfaceType->isValidValue($object));
        $this->assertFalse($interfaceType->isValidValue('invalid object'));
    }

}
