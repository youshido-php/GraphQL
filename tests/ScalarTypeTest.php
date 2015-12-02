<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:11 AM
*/

namespace Youshido\Tests;
require_once __DIR__ . '/../vendor/autoload.php';


use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\Tests\DataProvider\TestScalarDataProvider;

class ScalarTypeTest extends \PHPUnit_Framework_TestCase
{

    public function testTypeName()
    {
        foreach(TestScalarDataProvider::getTypesList() as $typeName) {
            $className = 'Youshido\GraphQL\Type\Scalar\\' . $typeName . 'Type';
            /** @var TypeInterface $object */
            $object = new $className();
            $this->assertEquals($typeName, $object->getName());
        }

    }

    public function testStringCoercion()
    {
        $object = new StringType();

        foreach(TestScalarDataProvider::getStringTestData() as list($data,$expectedResult)) {
            $this->assertEquals($expectedResult, $object->parseValue($data));
        }
    }

    public function testIntCoercion()
    {
        $object = new IntType();

        foreach(TestScalarDataProvider::getIntTestData() as list($data,$expectedResult)) {
            $this->assertEquals($expectedResult, $object->parseValue($data));
        }

    }

}
