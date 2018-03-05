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
 * created: 5/11/16 9:31 PM
 */

namespace Youshido\Tests\Library\Type;

use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;

class NonNullTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidParams(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);

        new NonNullType('invalid param');
    }

    public function testNonNullType(): void
    {
        $stringType      = new StringType();
        $nonNullType     = new NonNullType(new StringType());
        $nonNullOnString = new NonNullType(TypeMap::TYPE_STRING);
        $testArray       = ['a' => 'b'];

        $this->assertEquals($nonNullType->getName(), null, 'Empty non-null name');
        $this->assertEquals($nonNullType->getKind(), TypeMap::KIND_NON_NULL);
        $this->assertEquals($nonNullType->getType(), new NonNullType($stringType));
        $this->assertEquals($nonNullType->getNullableType(), $stringType);
        $this->assertEquals($nonNullType->getNullableType(), $nonNullOnString->getNullableType());
        $this->assertEquals($nonNullType->getNamedType(), $stringType);
        $this->assertEquals($nonNullType->getTypeOf(), $stringType);
        $this->assertEquals($nonNullType->isCompositeType(), true);
        $this->assertEquals(TypeService::isAbstractType($nonNullType), false);
        $this->assertFalse($nonNullType->isValidValue(null));
        $this->assertTrue($nonNullType->isValidValue($stringType));
        $this->assertFalse($nonNullType->isValidValue(new \stdClass()));
        $this->assertEquals($nonNullType->parseValue($testArray), '');
        $this->assertEquals($nonNullType->resolve($testArray), $testArray);
    }
}
