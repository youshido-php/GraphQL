<?php

namespace Youshido\Tests\Library\Type;

use Youshido\GraphQL\Type\Enum\EnumType;
use Youshido\GraphQL\Type\TypeKind;
use Youshido\Tests\DataProvider\TestEnumType;

/**
 * Class EnumTypeTest
 */
class EnumTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidInlineCreation()
    {
        new EnumType([]);
    }

    /**
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidEmptyParams()
    {
        new EnumType([
            'values' => [],
        ]);
    }

    /**
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidValueParams()
    {
        new EnumType([
            'values' => [
                'test'  => 'asd',
                'value' => 'asdasd',
            ],
        ]);
    }

    /**
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     */
    public function testExistingNameParams()
    {
        new EnumType([
            'values' => [
                [
                    'test'  => 'asd',
                    'value' => 'asdasd',
                ],
            ],
        ]);
    }

    /**
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidNameParams()
    {
        new EnumType([
            'values' => [
                [
                    'name'  => false,
                    'value' => 'asdasd',
                ],
            ],
        ]);
    }

    /**
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     */
    public function testWithoutValueParams()
    {
        new EnumType([
            'values' => [
                [
                    'name' => 'TEST_ENUM',
                ],
            ],
        ]);
    }

    public function testNormalCreatingParams()
    {
        $valuesData = [
            [
                'name'  => 'ENABLE',
                'value' => true,
            ],
            [
                'name'  => 'DISABLE',
                'value' => 'disable',
            ],
        ];
        $enumType   = new EnumType([
            'name'   => 'BoolEnum',
            'values' => $valuesData,
        ]);

        $this->assertEquals($enumType->getKind(), TypeKind::KIND_ENUM);
        $this->assertEquals($enumType->getName(), 'BoolEnum');
        $this->assertEquals($enumType->getNamedType(), $enumType);

        $this->assertFalse($enumType->isValidValue($enumType));
        $this->assertTrue($enumType->isValidValue(null));

        $this->assertTrue($enumType->isValidValue(true));
        $this->assertTrue($enumType->isValidValue('disable'));

        $this->assertNull($enumType->serialize('invalid value'));
        $this->assertNull($enumType->parseValue('invalid literal'));
        $this->assertTrue($enumType->parseValue('ENABLE'));

        $this->assertEquals($valuesData, $enumType->getValues());
    }

    public function testExtendedObject()
    {
        $testEnumType = new TestEnumType();
        $this->assertEquals('TestEnum', $testEnumType->getName());
    }
}
