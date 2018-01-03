<?php

namespace Youshido\Tests\Library\Type;

use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\FloatType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeKind;
use Youshido\Tests\DataProvider\TestScalarDataProvider;

/**
 * Class ScalarTypeTest
 */
class ScalarTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testScalarPrimitives()
    {
        $scalarTypes = [
            new IntType(),
            new FloatType(),
            new StringType(),
            new BooleanType(),
            new IdType(),
        ];

        foreach ($scalarTypes as $scalarType) {
            $typeName       = $scalarType->getName();
            $testDataMethod = 'get' . $typeName . 'TestData';

            $this->assertNotEmpty($scalarType->getDescription());
            $this->assertEquals($scalarType->getKind(), TypeKind::KIND_SCALAR);
            $this->assertEquals($scalarType->isCompositeType(), false);
            $this->assertEquals($scalarType, $scalarType->getNamedType());
            $this->assertNull($scalarType->getConfig());

            foreach ((array) TestScalarDataProvider::$testDataMethod() as list($data, $serialized, $isValid)) {

                $this->assertSerialization($scalarType, $data, $serialized);
                $this->assertParse($scalarType, $data, $serialized);

                if ($isValid) {
                    $this->assertTrue($scalarType->isValidValue($data), $typeName . ' validation for :' . serialize($data));
                } else {
                    $this->assertFalse($scalarType->isValidValue($data), $typeName . ' validation for :' . serialize($data));
                }
            }
        }

        $this->assertEquals('String', (new StringType())->getName());
    }

    private function assertSerialization(AbstractScalarType $object, $input, $expected)
    {
        $this->assertEquals($expected, $object->serialize($input), $object->getName() . ' serialize for: ' . serialize($input));
    }

    private function assertParse(AbstractScalarType $object, $input, $expected)
    {
        $parsed = $object->parseValue($input);
        $this->assertEquals($expected, $parsed, $object->getName() . ' parse for: ' . serialize($input));
    }
}
