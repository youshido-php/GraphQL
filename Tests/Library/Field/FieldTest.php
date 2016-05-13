<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/12/16 7:14 PM
*/

namespace Youshido\Tests\Library;


use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

class FieldTest extends \PHPUnit_Framework_TestCase
{

    public function testCreation()
    {
        $field = new Field([
            'name' => 'id',
            'type' => new IdType()
        ]);
        $this->assertEquals('id', $field->getName());
        $this->assertNull($field->resolve('data', null, null));

        $fieldWithResolve = new Field([
            'name' => 'title',
            'type' => new StringType(),
            'resolve' => function($value, $args, AbstractType $type) {
                return $type->serialize($value);
            }
        ]);

        $this->assertEquals('true', $fieldWithResolve->resolve(true), 'Resolve bool to string');
        $this->assertEquals('CTO', $fieldWithResolve->resolve('CTO'));
    }

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testInvalidStringType()
    {
        new Field([
            'name' => 'id',
            'type' => 'invalid type'
        ]);
    }

}
