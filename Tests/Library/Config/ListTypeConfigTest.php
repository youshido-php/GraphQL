<?php

namespace Youshido\Tests\Library\Config;

use Youshido\GraphQL\Config\Object\ListTypeConfig;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Class ListTypeConfigTest
 */
class ListTypeConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testCreating()
    {
        $config = new ListTypeConfig([
            'itemType' => new StringType(),
        ]);

        $this->assertEquals(new StringType(), $config->getItemType());

        $config->setItemType(new IntType());
        $this->assertEquals(new IntType(), $config->getItemType());
    }

    /**
     * @param array $config
     *
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     * @dataProvider invalidConfigs
     */
    public function testInvalidConfigs(array $config)
    {
        $config = new ListTypeConfig($config);
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
                    'name' => 'test'
                ],
            ],
            [
                [
                    'itemType' => null
                ],
            ],
            [
                [
                    'itemType' => 'test'
                ],
            ],
            [
                [
                    'itemType' => ''
                ],
            ],
        ];
    }
}
