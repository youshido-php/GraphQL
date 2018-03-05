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
/**
 * Date: 13.05.16.
 */

namespace Youshido\tests\Library\Type;

use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\Union\UnionType;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Youshido\Tests\DataProvider\TestObjectType;
use Youshido\Tests\DataProvider\TestUnionType;

class UnionTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testInlineCreation(): void
    {
        $object = new ObjectType([
            'name'   => 'TestObject',
            'fields' => ['id' => ['type' => new IntType()]],
        ]);

        $type = new UnionType([
            'name'        => 'Car',
            'description' => 'Union collect cars types',
            'types'       => [
                new TestObjectType(),
                $object,
            ],
            'resolveType' => static function ($type) {
                return $type;
            },
        ]);

        $this->assertEquals('Car', $type->getName());
        $this->assertEquals('Union collect cars types', $type->getDescription());
        $this->assertEquals([new TestObjectType(), $object], $type->getTypes());
        $this->assertEquals('test', $type->resolveType('test'));
        $this->assertEquals(TypeMap::KIND_UNION, $type->getKind());
        $this->assertEquals($type, $type->getNamedType());
        $this->assertTrue($type->isValidValue(true));
    }

    public function testObjectCreation(): void
    {
        $type = new TestUnionType();

        $this->assertEquals('TestUnion', $type->getName());
        $this->assertEquals('Union collect cars types', $type->getDescription());
        $this->assertEquals([new TestObjectType()], $type->getTypes());
        $this->assertEquals('test', $type->resolveType('test'));
    }


    public function testInvalidTypesWithScalar(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);

        $type = new UnionType([
            'name'        => 'Car',
            'description' => 'Union collect cars types',
            'types'       => [
                'test', new IntType(),
            ],
            'resolveType' => static function ($type) {
                return $type;
            },
        ]);
        ConfigValidator::getInstance()->assertValidConfig($type->getConfig());
    }


    public function testInvalidTypes(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);

        $type = new UnionType([
            'name'        => 'Car',
            'description' => 'Union collect cars types',
            'types'       => [
                new IntType(),
            ],
            'resolveType' => static function ($type) {
                return $type;
            },
        ]);
        ConfigValidator::getInstance()->assertValidConfig($type->getConfig());
    }
}
