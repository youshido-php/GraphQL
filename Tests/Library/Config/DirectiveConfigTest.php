<?php

namespace Youshido\Tests\Library\Config;

use Youshido\GraphQL\Config\Directive\DirectiveConfig;
use Youshido\GraphQL\Directive\DirectiveLocation;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;

/**
 * Class DirectiveConfigTest
 */
class DirectiveConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $config = new DirectiveConfig([
            'name'      => 'Directive',
            'args'      => [
                'id' => new IdType(),
            ],
            'locations' => [
                DirectiveLocation::QUERY,
            ],
        ]);

        $this->assertEquals('Directive', $config->getName());
        $config->setName('test');
        $this->assertEquals('test', $config->getName());

        $this->assertNull($config->getDescription());
        $config->setDescription('desc');
        $this->assertEquals('desc', $config->getDescription());

        $this->assertEquals([DirectiveLocation::QUERY], $config->getLocations());
        $config->setLocations([DirectiveLocation::MUTATION]);
        $this->assertEquals([DirectiveLocation::MUTATION], $config->getLocations());

        $this->assertTrue($config->hasArguments());
        $this->assertTrue($config->hasArgument('id'));
        $this->assertEquals([
            'id' => new InputField([
                'name' => 'id',
                'type' => new IdType(),
            ]),
        ], $config->getArguments());
    }

    /**
     * @param array $config
     *
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     * @dataProvider invalidConfigs
     */
    public function testInvalidConfigs(array $config)
    {
        $config = new DirectiveConfig($config);
        $config->validate();
    }

    public function invalidConfigs()
    {
        $args = [
            'id' => new IdType(),
        ];

        return [
            [
                [
                    'name'      => 'name',
                    'locations' => [DirectiveLocation::QUERY],
                    'args'      => [
                        new Field([
                            'name' => 'test',
                            'type' => new IntType(),
                        ]),
                    ],
                ],
            ],
            [
                [],
            ],
            [
                [
                    'name'      => '',
                    'locations' => [
                        DirectiveLocation::QUERY,
                    ],
                    'args'      => $args,
                ],
            ],
            [
                [
                    'name'      => null,
                    'locations' => [
                        DirectiveLocation::QUERY,
                    ],
                    'args'      => $args,
                ],
            ],
            [
                [
                    'name'      => 'name',
                    'locations' => [
                        'test',
                    ],
                    'args'      => $args,
                ],
            ],
            [
                [
                    'name'      => 'name',
                    'locations' => null,
                    'args'      => $args,
                ],
            ],
            [
                [
                    'name'      => 'name',
                    'locations' => 'a',
                    'args'      => $args,
                ],
            ],
        ];
    }
}
