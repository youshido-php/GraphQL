<?php

namespace Youshido\Tests\Library\Config;

use Youshido\GraphQL\Config\Object\UnionTypeConfig;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class UnionTypeConfigTest
 */
class UnionTypeConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $objectType = new ObjectType([
            'name'   => 'Object',
            'fields' => [
                'test' => new StringType(),
            ],
        ]);
        $config     = new UnionTypeConfig([
            'name'        => 'Union',
            'types'       => [$objectType],
            'resolveType' => function () {

            },
        ]);

        $this->assertEquals($config->getName(), 'Union');

        $config->setName('test2');
        $this->assertEquals($config->getName(), 'test2');

        $this->assertNull($config->getDescription());
        $config->setDescription('desc');
        $this->assertEquals('desc', $config->getDescription());

        $this->assertEquals(function () {
        }, $config->getResolveType());
        $config->setResolveType('null');
        $this->assertEquals('null', $config->getResolveType());

        $this->assertEquals([$objectType], $config->getTypes());
        $objectType2 = new ObjectType([
            'name'   => 'Test',
            'fields' => [
                'id' => new IdType(),
            ],
        ]);
        $config->setTypes([$objectType2]);
        $this->assertEquals([$objectType2], $config->getTypes());
    }

    /**
     * @param array $config
     *
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     * @dataProvider invalidConfigs
     */
    public function testInvalidConfigs(array $config)
    {
        $config = new UnionTypeConfig($config);
        $config->validate();
    }

    public function invalidConfigs()
    {
        $objectType = new ObjectType([
            'name'   => 'Object',
            'fields' => [
                'test' => new StringType(),
            ],
        ]);

        return [
            [
                [],
            ],
            [
                [
                    'name' => '',
                ],
            ],
            [
                [
                    'name'        => '',
                    'types'       => [$objectType],
                    'resolveType' => function () {
                    },
                ],
            ],
            [
                [
                    'name'        => '',
                    'types'       => [$objectType],
                    'resolveType' => function () {
                    },
                ],
            ],
            [
                [
                    'name'        => 'name',
                    'types'       => [],
                    'resolveType' => function () {
                    },
                ],
            ],
            [
                [
                    'name'        => 'name',
                    'types'       => null,
                    'resolveType' => function () {
                    },
                ],
            ],
            [
                [
                    'name'        => 'name',
                    'types'       => [$objectType],
                    'resolveType' => null,
                ],
            ],
            [
                [
                    'name'        => 'name',
                    'types'       => [new IntType()],
                    'resolveType' => null,
                ],
            ],
        ];
    }
}
