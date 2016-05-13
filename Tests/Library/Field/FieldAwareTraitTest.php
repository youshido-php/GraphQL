<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/12/16 7:46 PM
*/

namespace Youshido\Tests\Library\Field;


use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class FieldAwareTraitTest extends \PHPUnit_Framework_TestCase
{

    public function testAddField()
    {
        $fieldsData = [
            'id' => [
                'type' => new IntType()
            ]
        ];
        $config     = new ObjectTypeConfig([
            'name'   => 'UserType',
            'fields' => $fieldsData
        ]);

        $this->assertTrue($config->hasFields());
        $this->assertEquals([
            'id' => new Field(['name' => 'id', 'type' => new IntType()]),
        ], $config->getFields());

        $config->addField('name', new StringType());
        $this->assertEquals([
            'id'   => new Field(['name' => 'id', 'type' => new IntType()]),
            'name' => new Field(['name' => 'name', 'type' => new StringType()])
        ], $config->getFields());

        $config->removeField('id');
        $this->assertEquals([
            'name' => new Field(['name' => 'name', 'type' => new StringType()])
        ], $config->getFields());

        $config->addFields([
            'id' => new Field(['name' => 'id', 'type' => new IntType()])
        ]);
        $this->assertEquals([
            'name' => new Field(['name' => 'name', 'type' => new StringType()]),
            'id'   => new Field(['name' => 'id', 'type' => new IntType()]),
        ], $config->getFields());

        $config->addFields([
            new Field(['name' => 'level', 'type' => new IntType()])
        ]);
        $this->assertEquals([
            'name'  => new Field(['name' => 'name', 'type' => new StringType()]),
            'id'    => new Field(['name' => 'id', 'type' => new IntType()]),
            'level' => new Field(['name' => 'level', 'type' => new IntType()]),
        ], $config->getFields());

    }

}
