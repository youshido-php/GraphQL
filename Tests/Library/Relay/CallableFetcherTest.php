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
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 5/17/16 11:56 AM
 */

namespace Youshido\Tests\Library\Relay;

use Youshido\GraphQL\Relay\Fetcher\CallableFetcher;
use Youshido\Tests\DataProvider\TestObjectType;

class CallableFetcherTest extends \PHPUnit_Framework_TestCase
{
    public function testMethods(): void
    {
        $fetcher = new CallableFetcher(static function ($type, $id) {
            return ['name' => $type . ' Name', 'id' => $id];
        }, static function ($object) {
            return $object;
        });
        $this->assertEquals([
            'name' => 'User Name',
            'id'   => 12,
        ], $fetcher->resolveNode('User', 12));

        $object = new TestObjectType();
        $this->assertEquals($object, $fetcher->resolveType($object));
    }
}
