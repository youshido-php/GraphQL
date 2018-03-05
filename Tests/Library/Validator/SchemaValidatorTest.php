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
 * created: 5/15/16 4:04 PM
 */

namespace Youshido\Tests\Library\Validator;

use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Validator\SchemaValidator\SchemaValidator;
use Youshido\Tests\DataProvider\TestEmptySchema;
use Youshido\Tests\DataProvider\TestInterfaceType;

class SchemaValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidSchema(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);

        $validator = new SchemaValidator();
        $validator->validate(new TestEmptySchema());
    }


    public function testInvalidInterfacesSimpleType(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        $this->expectExceptionMessage('Implementation of TestInterface is invalid for the field name');

        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => new ObjectType([
                        'name'   => 'User',
                        'fields' => [
                            'name' => new IntType(),
                        ],
                        'interfaces' => [new TestInterfaceType()],
                    ]),
                ],
            ]),
        ]);

        $validator = new SchemaValidator();
        $validator->validate($schema);
    }


    public function testInvalidInterfacesCompositeType(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        $this->expectExceptionMessage('Implementation of TestInterface is invalid for the field name');

        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => new ObjectType([
                        'name'   => 'User',
                        'fields' => [
                            'name' => new NonNullType(new StringType()),
                        ],
                        'interfaces' => [new TestInterfaceType()],
                    ]),
                ],
            ]),
        ]);

        $validator = new SchemaValidator();
        $validator->validate($schema);
    }


    public function testInvalidInterfaces(): void
    {
        $this->expectException(\Youshido\GraphQL\Exception\ConfigurationException::class);
        $this->expectExceptionMessage('Implementation of TestInterface is invalid for the field name');

        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => new ObjectType([
                        'name'   => 'User',
                        'fields' => [
                            'name' => new IntType(),
                        ],
                        'interfaces' => [new TestInterfaceType()],
                    ]),
                ],
            ]),
        ]);

        $validator = new SchemaValidator();
        $validator->validate($schema);
    }

    public function testValidSchema(): void
    {
        $schema = new Schema([
            'query' => new ObjectType([
                'name'   => 'RootQuery',
                'fields' => [
                    'user' => new ObjectType([
                        'name'   => 'User',
                        'fields' => [
                            'name' => new StringType(),
                        ],
                        'interfaces' => [new TestInterfaceType()],
                    ]),
                ],
            ]),
        ]);

        $validator = new SchemaValidator();

        try {
            $validator->validate($schema);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }
    }
}
