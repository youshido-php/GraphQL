<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/12/16 10:11 PM
*/

namespace Youshido\Tests\Schema;


use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\Tests\DataProvider\TestEmptySchema;
use Youshido\Tests\DataProvider\TestObjectType;
use Youshido\Tests\DataProvider\TestSchema;

class SchemaTest extends \PHPUnit_Framework_TestCase
{

    public function testInlineSchema()
    {
        $queryType = new ObjectType([
            'name'   => 'RootQueryType',
            'fields' => [
                'currentTime' => [
                    'type'    => new StringType(),
                    'resolve' => function ($value, $args, $type) {
                        return 'May 5, 9:00am';
                    },
                    'args'    => [
                        'gmt' => [
                            'type' => new IntType(),
                            'default' => -5
                        ],
                    ],
                ]
            ]
        ]);
        /** it's probably wrong to not pass the default ARGS in the resolve */
        $this->assertEquals('May 5, 9:00am', $queryType->getField('currentTime')->resolve([]));
    }

    public function testStandaloneEmptySchema()
    {
        $schema = new TestEmptySchema();
        $this->assertFalse($schema->getQueryType()->hasFields());
    }

    public function testStandaloneSchema()
    {
        $schema = new TestSchema();
        $this->assertTrue($schema->getQueryType()->hasFields());
        $this->assertTrue($schema->getMutationType()->hasFields());

        $this->assertEquals(1, count($schema->getMutationType()->getFields()));

        $schema->addMutationField('changeUser', ['type' => new TestObjectType(), 'resolve' => function() {}]);
        $this->assertEquals(2, count($schema->getMutationType()->getFields()));

    }

}
