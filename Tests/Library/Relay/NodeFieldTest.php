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
 * created: 5/17/16 10:29 PM
 */

namespace Youshido\Tests\Library\Relay;

use Youshido\GraphQL\Relay\Fetcher\CallableFetcher;
use Youshido\GraphQL\Relay\Field\NodeField;

class NodeFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testMethods(): void
    {
        $fetcher = new CallableFetcher(static function (): void {
        }, static function (): void {
        });
        $field   = new NodeField($fetcher);

        $this->assertEquals('Fetches an object given its ID', $field->getDescription());
        $this->assertEquals('node', $field->getName());
        $this->assertEquals($fetcher, $field->getType()->getFetcher());
    }
}
