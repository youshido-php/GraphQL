<?php

namespace Youshido\Tests\Parser;

use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Location;

class VariableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if variable value equals expected value
     *
     * @dataProvider variableProvider
     */
    public function testGetValue($actual, $expected)
    {
        $var = new Variable('foo', 'bar', false, false, new Location(1,1));
        $var->setValue($actual);
        $this->assertEquals($var->getValue(), $expected);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Value is not set for variable "foo"
     */
    public function testGetNullValueException()
    {
        $var = new Variable('foo', 'bar', false, false, new Location(1,1));
        $var->getValue();
    }

    /**
     * @return array Array of <mixed: value to set, mixed: expected value>
     */
    public static function variableProvider()
    {
        return [
            [
                0,
                0
            ]
        ];
    }
}