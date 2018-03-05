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
 * created: 5/11/16 10:25 PM
 */

namespace Youshido\Tests\Library\Utilities;

use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;
use Youshido\Tests\DataProvider\TestInterfaceType;
use Youshido\Tests\DataProvider\TestObjectType;

class TypeUtilitiesTest extends \PHPUnit_Framework_TestCase
{
    public function testTypeService(): void
    {
        $this->assertTrue(TypeService::isScalarType(TypeMap::TYPE_STRING));
        $this->assertFalse(TypeService::isScalarType('gibberish'));
        $this->assertFalse(TypeService::isScalarType(new TestObjectType()));

        $stringType = new StringType();

        $this->assertFalse(TypeService::isInterface($stringType));
        $this->assertEquals(TypeService::resolveNamedType($stringType), $stringType);
        $this->assertNull(TypeService::resolveNamedType(null));
        $this->assertEquals(TypeService::resolveNamedType(123), $stringType);
    }


    public function testNamedTypeResolverException(): void
    {
        $this->expectException(\Exception::class);

        TypeService::resolveNamedType(['name' => 'test']);
    }

    public function testIsInputType(): void
    {
        $testType = new ObjectType(['name' => 'test', 'fields' => ['name' => new StringType()]]);
        $this->assertTrue(TypeService::isInputType(new StringType()));
        $this->assertTrue(TypeService::isInputType(TypeMap::TYPE_STRING));
        $this->assertFalse(TypeService::isInputType('invalid type'));
        $this->assertFalse(TypeService::isInputType($testType));
    }

    public function testIsAbstractType(): void
    {
        $this->assertTrue(TypeService::isAbstractType(new TestInterfaceType()));
        $this->assertFalse(TypeService::isAbstractType(new StringType()));
        $this->assertFalse(TypeService::isAbstractType('invalid type'));
    }

    public function testGetPropertyValue(): void
    {
        $arrayData = (new TestObjectType())->getData();

        $this->assertEquals('John', TypeService::getPropertyValue($arrayData, 'name'));
        $this->assertEquals('John', TypeService::getPropertyValue((object) $arrayData, 'name'));
    }
}
