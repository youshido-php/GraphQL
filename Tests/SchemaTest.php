<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 2:20 AM
*/

namespace Youshido\Tests;

use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\Tests\DataProvider\TestSchema;
use Youshido\Tests\DataProvider\UserType;

class SchemaTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testInvalidConfigExtraField()
    {
        $invalidSchemaConfig = [
            'name'   => 'TestSChema',
            'fields' => []
        ];

        new Schema($invalidSchemaConfig);
    }

    public function testInlineCreated()
    {
        $schema = new Schema(
            [
                'name'  => 'GeoSchema',
                'query' => new ObjectType(
                    [
                        'name'   => 'RootQuery',
                        'fields' => [
                            'users' => new ListType(['item' => new UserType()])
                        ]
                    ])
            ]);

        $this->assertEquals('GeoSchema', $schema->getName());
    }

    public function testClassUsage()
    {
        $schema = new TestSchema();
        $this->assertEquals('TestSchema', $schema->getName());

        $fields = $schema->getQueryType()->getConfig()->getFields();
        $this->assertEquals(1, count($fields));

        $this->assertEquals('users', $fields['users']->getName());
    }
}
