<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/12/16 5:02 PM
*/

namespace Youshido\Tests\Library\Type;


use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\Tests\DataProvider\TestInterfaceType;

class InterfaceTypeTest extends \PHPUnit_Framework_TestCase
{


    public function testInterfaceMethods()
    {
        $interface = new TestInterfaceType();
        $this->assertEquals($interface->getNamedType(), $interface->getType());

        $object = new ObjectType([
            'name' => 'Test',
            'fields' => [
                'name' => new StringType()
            ],
            'interfaces' => [$interface],
        ]);
        $this->assertEquals([$interface], $object->getInterfaces());
        $this->assertTrue($interface->isValidValue($object));
        $this->assertFalse($interface->isValidValue('invalid object'));

        $this->assertEquals($interface->serialize($object), $object);
    }

}
