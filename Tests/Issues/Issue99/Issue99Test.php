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

namespace Youshido\Tests\Issues\Issue99;

use Youshido\GraphQL\Execution\Processor;

/**
 * User: m-naw
 * Date: 2/02/17.
 */
class Issue99Test extends \PHPUnit_Framework_TestCase
{
    public const BUG_NOT_EXISTS_VALUE = 'bug not exists';

    public const BUG_EXISTS_VALUE = 'bug exists';

    public function testQueryDateTimeTypeWithDateParameter(): void
    {
        $schema    = new Issue99Schema();
        $processor = new Processor($schema);
        $processor->processPayload(\sprintf('{ items{id, custom(argX: {x: "%s"}){ value } } }', self::BUG_NOT_EXISTS_VALUE));
        $res = $processor->getResponseData();

        self::assertTrue(isset($res['data']['items']));

        foreach ($res['data']['items'] as $item) {
            self::assertTrue(isset($item['custom']['value']));
            self::assertEquals(self::BUG_NOT_EXISTS_VALUE, $item['custom']['value']);
        }
    }
}
