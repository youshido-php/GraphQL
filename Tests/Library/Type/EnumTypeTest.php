<?php
/**
 * Date: 12.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\Tests\Library\Type;


use Youshido\GraphQL\Type\Enum\EnumType;
use Youshido\GraphQL\Type\TypeMap;

class EnumTypeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testInvalidInlineCreation()
    {
        new EnumType([]);
    }

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testInvalidEmptyParams()
    {
        new EnumType([
            'values' => []
        ]);
    }

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testInvalidValueParams()
    {
        new EnumType([
            'values' => [
                'test'  => 'asd',
                'value' => 'asdasd'
            ]
        ]);
    }

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testExistingNameParams()
    {
        new EnumType([
            'values' => [
                [
                    'test'  => 'asd',
                    'value' => 'asdasd'
                ]
            ]
        ]);
    }

    /**
     * @expectedException Youshido\GraphQL\Validator\Exception\ConfigurationException
     */
    public function testInvalidNameParams()
    {
        new EnumType([
            'values' => [
                [
                    'name'  => false,
                    'value' => 'asdasd'
                ]
            ]
        ]);
    }

    public function testNormalCreatingParam()
    {
        $objectType = new EnumType([
            'name'   => 'Bool',
            'values' => [
                [
                    'name'  => 'ENABLE',
                    'value' => true
                ],
                [
                    'name'  => 'DISABLE',
                    'value' => 'disable'
                ]
            ]
        ]);

        $this->assertEquals($objectType->getKind(), TypeMap::KIND_ENUM);
        $this->assertEquals($objectType->getName(), 'Bool');
        $this->assertEquals($objectType->getType(), $objectType);
        $this->assertEquals($objectType->getNamedType(), $objectType);

        $this->assertFalse($objectType->isValidValue($objectType));
        $this->assertFalse($objectType->isValidValue(null));

        $this->assertTrue($objectType->isValidValue(true));
        $this->assertTrue($objectType->isValidValue('disable'));

        $this->assertNull($objectType->serialize('NOT EXIST'));
        $this->assertTrue($objectType->serialize('ENABLE'));
    }

}