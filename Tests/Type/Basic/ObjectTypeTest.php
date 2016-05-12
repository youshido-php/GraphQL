<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/11/16 9:43 PM
*/

namespace Youshido\Tests\Basic;


use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

class ObjectTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testCreatingInvalidObject()
    {
        new ObjectType([]);
    }

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testInvalidNameParam()
    {
        new ObjectType([
            'name' => null
        ]);
    }

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testInvalidFieldsParam()
    {
        new ObjectType([
            'name' => 'SomeName',
            'fields' => []
        ]);
    }

    public function testNormalCreatingParam()
    {
        $objectType = new ObjectType([
            'name' => 'Post',
            'fields' => [
                'id' => new IntType()
            ]
        ]);
        $this->assertEquals($objectType->getName(), 'Post');
    }

}
