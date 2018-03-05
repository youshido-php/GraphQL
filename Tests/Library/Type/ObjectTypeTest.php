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
 * created: 5/11/16 9:43 PM
 */

namespace Youshido\Tests\Library\Type;

use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Youshido\Tests\DataProvider\TestMutationObjectType;
use Youshido\Tests\DataProvider\TestObjectType;

class ObjectTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatingInvalidObject(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);

        new ObjectType([]);
    }


    public function testInvalidNameParam(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);

        $type = new ObjectType([
            'name' => null,
        ]);
        ConfigValidator::getInstance()->assertValidConfig($type->getConfig());
    }


    public function testInvalidFieldsParam(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);

        $type = new ObjectType([
            'name'   => 'SomeName',
            'fields' => [],
        ]);
        ConfigValidator::getInstance()->assertValidConfig($type->getConfig());
    }


    public function testSerialize(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $object = new ObjectType([
            'name'   => 'SomeName',
            'fields' => [
                'name' => new StringType(),
            ],
        ]);
        $object->serialize([]);
    }

    public function testNormalCreatingParam(): void
    {
        $objectType = new ObjectType([
            'name'   => 'Post',
            'fields' => [
                'id' => new IntType(),
            ],
            'description' => 'Post type description',
        ]);
        $this->assertEquals($objectType->getKind(), TypeMap::KIND_OBJECT);
        $this->assertEquals($objectType->getName(), 'Post');
        $this->assertEquals($objectType->getType(), $objectType);
        $this->assertEquals($objectType->getType()->getName(), 'Post');
        $this->assertEquals($objectType->getNamedType(), $objectType);

        $this->assertEmpty($objectType->getInterfaces());
        $this->assertTrue($objectType->isValidValue($objectType));
        $this->assertTrue($objectType->isValidValue(null));

        $this->assertEquals('Post type description', $objectType->getDescription());
    }

    public function testFieldsTrait(): void
    {
        $idField   = new Field(['name' => 'id', 'type' => new IntType()]);
        $nameField = new Field(['name' => 'name', 'type' => new StringType()]);

        $objectType = new ObjectType([
            'name'   => 'Post',
            'fields' => [
                $idField,
            ],
            'description' => 'Post type description',
        ]);
        $this->assertTrue($objectType->hasFields());
        $this->assertEquals([
            'id' => $idField,
        ], $objectType->getFields());

        $objectType->addField($nameField);
        $this->assertEquals([
            'id'   => $idField,
            'name' => $nameField,
        ], $objectType->getFields());
    }

    public function testExtendedClass(): void
    {
        $objectType = new TestObjectType();
        $this->assertEquals($objectType->getName(), 'TestObject');
        $this->assertEquals($objectType->getType(), $objectType, 'test type of extended object');

        $this->assertNull($objectType->getDescription());
    }

    public function testMutationObjectClass(): void
    {
        $mutation = new TestMutationObjectType();
        $this->assertEquals(new StringType(), $mutation->getType());
    }
}
