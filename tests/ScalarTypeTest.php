<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:11 AM
*/

namespace Youshido\Tests;


use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;

class ScalarTypeTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        require_once __DIR__ . '/../vendor/autoload.php';
    }


    public function testStringCoercion()
    {
        $object = new StringType();

        $this->assertEquals('String', $object->getName());
        $this->assertEquals('Test', $object->parseValue('Test'));
        $this->assertEquals('1', $object->parseValue(1));
        $this->assertEquals('true', $object->parseValue(true));
        $this->assertEquals('false', $object->parseValue(false));
    }

    public function testIntCoercion()
    {
        $object = new IntType();

        $this->assertEquals(1, $object->parseValue(1));
        $this->assertEquals(-5, $object->parseValue(-5));
        $this->assertEquals(null, $object->parseValue("1"));
        $this->assertEquals(null, $object->parseValue('Test'));
        $this->assertEquals(null, $object->parseValue(false));
        $this->assertEquals(null, $object->parseValue(1.5));
    }

}
