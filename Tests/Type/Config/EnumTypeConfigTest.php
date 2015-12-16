<?php
/**
 * Date: 07.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Type\Config;


use Youshido\GraphQL\Type\Object\EnumType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\Tests\StarWars\Schema\EpisodeEnum;

class EnumTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testExtendConfig()
    {
        $type = new EpisodeEnum();

        $this->assertEquals('Episode', $type->getName());
        $this->assertEquals(TypeMap::KIND_ENUM, $type->getKind());
        $this->assertEmpty($type->getConfig()->getFields());

        $values = $type->getValues();

        $this->assertArrayHasKey('JEDI', $values);
        $this->assertArrayHasKey('value', $values['JEDI']);
    }

    public function testConfig()
    {
        $type = new EnumType([
            'name' => 'Episode',
            'values' => [
                'NEWHOPE' => [
                    'value'       => 4,
                    'description' => 'Released in 1977.'
                ],
                'EMPIRE'  => [
                    'value'       => 5,
                    'description' => 'Released in 1980.'
                ],
                'JEDI'    => [
                    'value'       => 6,
                    'description' => 'Released in 1983.'
                ],
            ]
        ]);

        $this->assertEquals('Episode', $type->getName());
        $this->assertEquals(TypeMap::KIND_ENUM, $type->getKind());
        $this->assertEmpty($type->getConfig()->getFields());

        $values = $type->getValues();

        $this->assertArrayHasKey('JEDI', $values);
        $this->assertArrayHasKey('value', $values['JEDI']);
    }

}