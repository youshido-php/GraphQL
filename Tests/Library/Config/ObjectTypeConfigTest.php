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

use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Youshido\Tests\DataProvider\TestInterfaceType;

class ObjectTypeConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation(): void
    {
        $config = new ObjectTypeConfig(['name' => 'Test'], null, false);
        $this->assertEquals($config->getName(), 'Test', 'Normal creation');
    }


    public function testInvalidConfigNoFields(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);

        ConfigValidator::getInstance()->assertValidConfig(
            new ObjectTypeConfig(['name' => 'Test'], null, true)
        );
    }


    public function testInvalidConfigInvalidInterface(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);

        ConfigValidator::getInstance()->assertValidConfig(
            new ObjectTypeConfig(['name' => 'Test', 'interfaces' => ['Invalid interface']], null, false)
        );
    }

    public function testInterfaces(): void
    {
        $testInterfaceType = new TestInterfaceType();
        $config            = new ObjectTypeConfig(['name' => 'Test', 'interfaces' => [$testInterfaceType]], null, false);
        $this->assertEquals($config->getInterfaces(), [$testInterfaceType]);
    }
}
