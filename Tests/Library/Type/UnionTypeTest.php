<?php
/**
 * Date: 13.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\tests\Library\Type;


use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Union\UnionType;
use Youshido\Tests\DataProvider\TestUnionType;

class UnionTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testInlineCreation()
    {
        $type = new UnionType([
            'name'        => 'Car',
            'description' => 'Union collect cars types',
            'types'       => [
                new IntType(),
                new StringType()
            ],
            'resolveType' => function ($type) {
                return $type;
            }
        ]);

        $this->assertEquals('Car', $type->getName());
        $this->assertEquals('Union collect cars types', $type->getDescription());
        $this->assertEquals([new IntType(), new StringType()], $type->getTypes());
        $this->assertEquals('test', $type->resolveType('test'));
    }

    public function testObjectCreation()
    {
        $type = new TestUnionType();

        $this->assertEquals('TestUnion', $type->getName());
        $this->assertEquals('Union collect cars types', $type->getDescription());
        $this->assertEquals([new IntType(), new StringType()], $type->getTypes());
        $this->assertEquals('test', $type->resolveType('test'));
    }

}