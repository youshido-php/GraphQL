<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
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
use Youshido\Tests\DataProvider\TestExtendedType;
use Youshido\Tests\DataProvider\TestInterfaceType;

class InterfaceTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testInterfaceMethods(): void
    {
        $interface = new TestInterfaceType();
        $this->assertEquals($interface->getNamedType(), $interface->getType());
        $nameField = new Field(['name' => 'name', 'type' => new StringType()]);
        $nameField->getName();

        $this->assertEquals(
            ['name' => $nameField],
            $interface->getFields()
        );

        $object = new ObjectType([
            'name'   => 'Test',
            'fields' => [
                'name' => new StringType(),
            ],
            'interfaces' => [$interface],
        ]);
        $this->assertEquals([$interface], $object->getInterfaces());
        $this->assertTrue($interface->isValidValue($object));
        $this->assertFalse($interface->isValidValue('invalid object'));

        $this->assertEquals($interface->serialize($object), $object);

        $interfaceType = new InterfaceType([
            'name'   => 'UserInterface',
            'fields' => [
                'name' => new StringType(),
            ],
            'resolveType' => static function ($object) {
                return $object;
            },
        ]);
        $this->assertEquals('UserInterface', $interfaceType->getName());

        $this->assertEquals($object, $interfaceType->resolveType($object));

        $this->assertTrue($interfaceType->isValidValue($object));
        $this->assertFalse($interfaceType->isValidValue('invalid object'));
    }

    public function testApplyInterface(): void
    {
        $extendedType = new TestExtendedType();

        $this->assertArrayHasKey('ownField', $extendedType->getFields());
        $this->assertArrayHasKey('name', $extendedType->getFields());
    }
}
