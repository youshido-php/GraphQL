<?php

namespace Youshido\Tests\Library\Config;

use Youshido\GraphQL\Config\Schema\SchemaConfig;
use Youshido\GraphQL\Directive\IncludeDirective;
use Youshido\GraphQL\Directive\SkipDirective;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;

/**
 * Class SchemaConfigTest
 */
class SchemaConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $query  = new ObjectType([
            'name'   => 'RootQuery',
            'fields' => [
                'id' => new IdType(),
            ],
        ]);
        $config = new SchemaConfig([
            'query'      => $query,
            'directives' => [
                IncludeDirective::build(),
            ],
        ]);

        $this->assertEquals('Schema', $config->getName());
        $this->assertTrue(is_array($config->getTypes()));
        $this->assertTrue(is_array($config->getDirectives()));
        $this->assertNull($config->getMutation());
        $this->assertEquals($query, $config->getQuery());

        $config->addDirective(SkipDirective::build());
        $this->assertEquals([
            'include' => IncludeDirective::build(),
            'skip'    => SkipDirective::build(),
        ], $config->getDirectives());

        $config->addType($query);
        $this->assertEquals(['RootQuery' => $query], $config->getTypes());
    }
}
