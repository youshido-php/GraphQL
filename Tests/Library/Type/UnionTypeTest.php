<?php

namespace Youshido\tests\Library\Type;

use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfo;
use Youshido\GraphQL\Execution\ResolveInfo\ResolveInfoInterface;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\TypeKind;
use Youshido\GraphQL\Type\Union\UnionType;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;
use Youshido\Tests\DataProvider\TestObjectType;
use Youshido\Tests\DataProvider\TestUnionType;

/**
 * Class UnionTypeTest
 */
class UnionTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testInlineCreation()
    {
        $object = new ObjectType([
            'name' => 'TestObject',
            'fields' => ['id' => ['type' => new IntType()]]
        ]);

        $type = new UnionType([
            'name'        => 'Car',
            'description' => 'Union collect cars types',
            'types'       => [
                new TestObjectType(),
                $object
            ],
            'resolveType' => function ($type) {
                return $type;
            }
        ]);

        $this->assertEquals('Car', $type->getName());
        $this->assertEquals('Union collect cars types', $type->getDescription());
        $this->assertEquals([new TestObjectType(), $object], $type->getTypes());
        $this->assertEquals('test', $type->resolveType('test', $this->getMock(ResolveInfoInterface::class)));
        $this->assertEquals(TypeKind::KIND_UNION, $type->getKind());
        $this->assertEquals($type, $type->getNamedType());
        $this->assertTrue($type->isValidValue(true));
    }

    public function testObjectCreation()
    {
        $type = new TestUnionType();

        $this->assertEquals('TestUnion', $type->getName());
        $this->assertEquals('Union collect cars types', $type->getDescription());
        $this->assertEquals([new TestObjectType()], $type->getTypes());
        $this->assertEquals('test', $type->resolveType('test', $this->getMock(ResolveInfoInterface::class)));
    }

    /**
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidTypesWithScalar()
    {
        new UnionType([
            'name'        => 'Car',
            'description' => 'Union collect cars types',
            'types'       => [
                'test', new IntType()
            ],
            'resolveType' => function ($type) {
                return $type;
            }
        ]);
    }

    /**
     * @expectedException Youshido\GraphQL\Exception\ConfigurationException
     */
    public function testInvalidTypes()
    {
        new UnionType([
            'name'        => 'Car',
            'description' => 'Union collect cars types',
            'types'       => [
                new IntType()
            ],
            'resolveType' => function ($type) {
                return $type;
            }
        ]);
    }
}
