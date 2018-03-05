<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace Youshido\Tests\Parser;

use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Location;

class VariableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if variable value equals expected value.
     *
     * @dataProvider variableProvider
     *
     * @param mixed $actual
     * @param mixed $expected
     */
    public function testGetValue($actual, $expected): void
    {
        $var = new Variable('foo', 'bar', false, false, true, new Location(1, 1));
        $var->setValue($actual);
        $this->assertEquals($var->getValue(), $expected);
    }


    public function testGetNullValueException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Value is not set for variable "foo"');

        $var = new Variable('foo', 'bar', false, false, true, new Location(1, 1));
        $var->getValue();
    }

    public function testGetValueReturnsDefaultValueIfNoValueSet(): void
    {
        $var = new Variable('foo', 'bar', false, false, true, new Location(1, 1));
        $var->setDefaultValue('default-value');

        $this->assertEquals(
            'default-value',
            $var->getValue()
        );
    }

    public function testGetValueReturnsSetValueEvenWithDefaultValue(): void
    {
        $var = new Variable('foo', 'bar', false, false, true, new Location(1, 1));
        $var->setValue('real-value');
        $var->setDefaultValue('default-value');

        $this->assertEquals(
            'real-value',
            $var->getValue()
        );
    }

    public function testIndicatesDefaultValuePresent(): void
    {
        $var = new Variable('foo', 'bar', false, false, true, new Location(1, 1));
        $var->setDefaultValue('default-value');

        $this->assertTrue(
            $var->hasDefaultValue()
        );
    }

    public function testHasNoDefaultValue(): void
    {
        $var = new Variable('foo', 'bar', false, false, true, new Location(1, 1));

        $this->assertFalse(
            $var->hasDefaultValue()
        );
    }

    /**
     * @return array Array of <mixed: value to set, mixed: expected value>
     */
    public static function variableProvider()
    {
        return [
            [
                0,
                0,
            ],
        ];
    }
}
