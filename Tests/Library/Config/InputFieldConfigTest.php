<?php

namespace Youshido\Tests\Library\Config;

use Youshido\GraphQL\Config\Field\InputFieldConfig;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class InputFieldConfigTest
 */
class InputFieldConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidParams()
    {
        $config = new InputFieldConfig([
            'name' => 'Field',
            'type' => new StringType(),
        ]);

        $this->assertEquals('Field', $config->getName());
        $config->setName('test');
        $this->assertEquals('test', $config->getName());

        $this->assertEquals(new StringType(), $config->getType());
        $config->setType(new IdType());
        $this->assertEquals(new IdType(), $config->getType());

        $this->assertNull($config->getDescription());
        $config->setDescription('desc');
        $this->assertEquals('desc', $config->getDescription());

        $this->assertNull($config->getDeprecationReason());
        $config->setDeprecationReason('dep');
        $this->assertEquals('dep', $config->getDeprecationReason());

        $this->assertFalse($config->isDeprecated());
        $config->setIsDeprecated(true);
        $this->assertTrue($config->isDeprecated());
    }

    /**
     * @param array $config
     *
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     * @dataProvider invalidConfigs
     */
    public function testInvalidConfigs(array $config)
    {
        $config = new InputFieldConfig($config);
        $config->validate();
    }

    public function invalidConfigs()
    {
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
                    'name' => '',
                    'type' => new IntType(),
                    'cost' => 1,
                ],
            ],
            [
                [
                    'name' => 'test',
                    'type' => new \stdClass(),
                ],
            ],
        ];
    }
}
