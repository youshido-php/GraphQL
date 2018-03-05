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
 * created: 3:59 PM 5/17/16
 */

namespace Youshido\Tests\Library\Relay;

use Youshido\GraphQL\Relay\Node;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    public function testMethods(): void
    {
        $global     = Node::toGlobalId('user', 1);
        $fromGlobal = Node::fromGlobalId($global);

        $this->assertEquals('user', $fromGlobal[0]);
        $this->assertEquals(1, $fromGlobal[1]);
        $this->assertEquals([null, null], Node::fromGlobalId(null));
    }
}
