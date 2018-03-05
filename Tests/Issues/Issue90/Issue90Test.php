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

namespace Youshido\Tests\Issues\Issue90;

use Youshido\GraphQL\Execution\Processor;

/**
 * User: stefano.corallo
 * Date: 25/11/16
 * Time: 9.39.
 */
class Issue90Test extends \PHPUnit_Framework_TestCase
{
    public function testQueryDateTimeTypeWithDateParameter(): void
    {
        $schema    = new Issue90Schema();
        $processor = new Processor($schema);
        $processor->processPayload('query{ echo(date: "2016-11-25 09:53am") }');
        $res = $processor->getResponseData();

        self::assertCount(1, $res, 'Invalid response array received'); //only data expected

        self::assertNotNull($res['data']['echo'], 'Invalid echo response received');

        self::assertEquals('2016-11-25 09:53am', $res['data']['echo']);
    }

    public function testQueryDateTimeTypeWithoutParameter(): void
    {
        $processor = new Processor(new Issue90Schema());
        $processor->processPayload('query{ echo }');
        $res = $processor->getResponseData();

        self::assertCount(1, $res, 'Invalid response array received'); //only data expected

        self::assertNull($res['data']['echo']);
    }

    public function testQueryDateTimeTypeWithNullParameter(): void
    {
        $processor = new Processor(new Issue90Schema());
        $processor->processPayload('query{ echo(date: null) }');
        $res = $processor->getResponseData();

        self::assertCount(1, $res, 'Invalid response array received'); //only data expected

        self::assertNull($res['data']['echo'], 'Error Quering with explicit date null parameter ');
    }

    public function testMutatingDateTimeWithParameter(): void
    {
        $schema    = new Issue90Schema();
        $processor = new Processor($schema);
        $processor->processPayload('mutation{ echo(date: "2016-11-25 09:53am") }');
        $res = $processor->getResponseData();

        self::assertCount(1, $res, 'Invalid response array received'); //only data expected

        self::assertNotNull($res['data']['echo'], 'Invalid echo response received during mutation of date parameter');

        self::assertEquals('2016-11-25 09:53am', $res['data']['echo']);
    }

    public function testMutatingDateTimeWithExplicitNullParameter(): void
    {
        $schema    = new Issue90Schema();
        $processor = new Processor($schema);
        $processor->processPayload('mutation{ echo(date: null) }');
        $res = $processor->getResponseData();

        self::assertCount(1, $res, 'Invalid response array received'); //only data expected
        self::assertNull($res['data']['echo'], 'Invalid echo response received during mutation of date parameter with explicit null value');
    }
}
