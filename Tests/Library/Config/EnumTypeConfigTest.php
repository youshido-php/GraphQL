<?php

namespace Youshido\Tests\Library\Config;

use Youshido\GraphQL\Config\Object\EnumTypeConfig;

/**
 * Class EnumTypeConfigTest
 */
class EnumTypeConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $values = [
            ['name' => 'test', 'value' => 'test'],
        ];
        $config = new EnumTypeConfig([
            'name'   => 'Enum',
            'values' => $values,
        ]);

        $this->assertEquals($config->getName(), 'Enum');

        $config->setName('test2');
        $this->assertEquals($config->getName(), 'test2');

        $this->assertNull($config->getDescription());
        $config->setDescription('desc');
        $this->assertEquals('desc', $config->getDescription());

        $this->assertEquals($values, $config->getValues());
        $values = [
            ['name' => 'a', 'value' => '1'],
        ];
        $config->setValues($values);
        $this->assertEquals($values, $config->getValues());
    }

    /**
     * @param array $config
     *
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     * @dataProvider invalidConfigs
     */
    public function testInvalidConfigs(array $config)
    {
        $config = new EnumTypeConfig($config);
        $config->validate();
    }

    public function invalidConfigs()
    {
        $values = [
            ['name' => 'test', 'value' => 'test'],
        ];

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
                    'name'   => '',
                    'values' => $values,
                ],
            ],
            [
                [
                    'name'   => null,
                    'values' => $values,
                ],
            ],
            [
                [
                    'name'   => 'test',
                    'values' => '',
                ],
            ],
            [
                [
                    'name'   => 'test',
                    'values' => [],
                ],
            ],
            [
                [
                    'name'   => 'test',
                    'values' => [
                        '1', '2', 3,
                    ],
                ],
            ],
            [
                [
                    'name'   => 'test',
                    'values' => [
                        [
                            'name' => '123',
                        ],
                    ],
                ],
            ],
            [
                [
                    'name'   => 'test',
                    'values' => [
                        [
                            'value' => '123',
                        ],
                    ],
                ],
            ],
            [
                [
                    'name'   => 'test',
                    'values' => [
                        [
                            'name'  => '123',
                            'value' => '123',
                        ],
                    ],
                ],
            ],
        ];
    }
}
