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
 * created: 10:39 PM 5/18/16
 */

namespace Youshido\Tests\Library\Relay;

use Youshido\GraphQL\Relay\Connection\Connection;
use Youshido\GraphQL\Relay\Type\PageInfoType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\Tests\DataProvider\TestObjectType;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    public function testConnectionArgs(): void
    {
        $this->assertEquals([
            'after'  => ['type' => TypeMap::TYPE_STRING],
            'first'  => ['type' => TypeMap::TYPE_INT],
            'before' => ['type' => TypeMap::TYPE_STRING],
            'last'   => ['type' => TypeMap::TYPE_INT],

        ], Connection::connectionArgs());
    }

    public function testPageInfoType(): void
    {
        $type = new PageInfoType();
        $this->assertEquals('PageInfo', $type->getName());
        $this->assertEquals('Information about pagination in a connection.', $type->getDescription());
        $this->assertTrue($type->hasField('hasNextPage'));
        $this->assertTrue($type->hasField('hasPreviousPage'));
        $this->assertTrue($type->hasField('startCursor'));
        $this->assertTrue($type->hasField('endCursor'));
    }

    public function testEdgeDefinition(): void
    {
        $edgeType = Connection::edgeDefinition(new StringType(), 'user');
        $this->assertEquals('userEdge', $edgeType->getName());
        $this->assertTrue($edgeType->hasField('node'));
        $this->assertTrue($edgeType->hasField('cursor'));
    }

    public function testConnectionDefinition(): void
    {
        $connection = Connection::connectionDefinition(new TestObjectType(), 'user');
        $this->assertEquals($connection->getName(), 'userConnection');
        $this->assertTrue($connection->hasField('pageInfo'));
        $this->assertTrue($connection->hasField('edges'));
    }
}
