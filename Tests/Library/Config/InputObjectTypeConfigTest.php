<?php

namespace Youshido\Tests\Library\Config;

use Youshido\GraphQL\Config\Object\InputObjectTypeConfig;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class InputObjectTypeConfigTest
 */
class InputObjectTypeConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalCreating()
    {
        $config = new InputObjectTypeConfig([
            'name'   => 'test',
            'fields' => [
                'id' => new StringType(),
            ],
        ]);

        $this->assertEquals($config->getName(), 'test', 'Normal creation');
    }

    /**
     * @param array $config
     *
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     * @dataProvider invalidConfigs
     */
    public function testInvalidConfigs(array $config)
    {
        $config = new InputObjectTypeConfig($config);
        $config->validate();
    }

    public function invalidConfigs()
    {
        return [
            [
                [
                    'name' => '',
                ],
            ],
            [
                [
                    'name' => 'test',
                ],
            ],
            [
                [
                    'name'   => 'test',
                    'fields' => [],
                ],
            ],
            [
                [
                    'fields' => [
                        'id' => new StringType(),
                    ],
                ],
            ],
            [
                [
                    'name'   => '',
                    'fields' => [
                        'id' => new StringType(),
                    ],
                ],
            ],
        ];
    }

    public function testProps()
    {
        $config = new InputObjectTypeConfig([
            'name'   => 'test',
            'fields' => [
                'id' => new StringType(),
            ],
        ]);

        $this->assertEquals('test', $config->getName());
        $config->setName('test2');
        $this->assertEquals('test2', $config->getName());

        $this->assertTrue($config->hasField('id'));
        $this->assertTrue($config->hasFields());

        $config->removeField('id');
        $this->assertFalse($config->hasFields());

        $this->assertNull($config->getDescription());
        $config->setDescription('desc');
        $this->assertEquals('desc', $config->getDescription());
    }
}
