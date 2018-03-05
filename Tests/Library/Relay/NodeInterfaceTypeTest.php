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

namespace Youshido\Tests\Library\Relay;

/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 5/17/16 11:49 AM
 */

use Youshido\GraphQL\Relay\Fetcher\CallableFetcher;
use Youshido\GraphQL\Relay\NodeInterfaceType;
use Youshido\Tests\DataProvider\TestObjectType;

class NodeInterfaceTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testMethods(): void
    {
        $type       = new NodeInterfaceType();
        $testObject = new TestObjectType();

        $this->assertEquals('NodeInterface', $type->getName());
        $this->assertNull($type->getFetcher());
        $this->assertNull($type->resolveType($testObject));

        $fetcher = new CallableFetcher(static function (): void {
        }, static function () {
            return new TestObjectType();
        });
        $type->setFetcher($fetcher);
        $this->assertEquals($fetcher, $type->getFetcher());

        $this->assertEquals($testObject, $type->resolveType($testObject));
    }
}
