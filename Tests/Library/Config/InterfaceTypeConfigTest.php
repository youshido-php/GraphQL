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
 * created: 5/12/16 4:17 PM
 */

namespace Youshido\Tests\Library\Config;

use Youshido\GraphQL\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Youshido\Tests\DataProvider\TestInterfaceType;

class InterfaceTypeConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation(): void
    {
        $config = new InterfaceTypeConfig(['name' => 'Test'], null, false);
        $this->assertEquals($config->getName(), 'Test', 'Normal creation');
    }


    public function testConfigNoFields(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);

        ConfigValidator::getInstance()->assertValidConfig(
            new InterfaceTypeConfig(['name' => 'Test', 'resolveType' => static function (): void {
            }], null, true)
        );
    }


    public function testConfigNoResolve(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);

        ConfigValidator::getInstance()->assertValidConfig(
            new InterfaceTypeConfig(['name' => 'Test', 'fields' => ['id' => new IntType()]], null, true)
        );
    }


    public function testConfigInvalidResolve(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);

        $config = new InterfaceTypeConfig(['name' => 'Test', 'fields' => ['id' => new IntType()]], null, false);
        $config->resolveType(['invalid object']);
    }

    public function testInterfaces(): void
    {
        $interfaceConfig = new InterfaceTypeConfig([
            'name'        => 'Test',
            'fields'      => ['id' => new IntType()],
            'resolveType' => static function ($object) {
                return $object->getType();
            },
        ], null, true);
        $object = new ObjectType(['name' => 'User', 'fields' => ['name' => new StringType()]]);

        $this->assertEquals($interfaceConfig->getName(), 'Test');
        $this->assertEquals($interfaceConfig->resolveType($object), $object->getType());

        $testInterface                = new TestInterfaceType();
        $interfaceConfigWithNoResolve = new InterfaceTypeConfig([
            'name'   => 'Test',
            'fields' => ['id' => new IntType()],
        ], $testInterface, false);
        $this->assertEquals($interfaceConfigWithNoResolve->resolveType($object), $object);
    }
}
