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
 * created: 10:51 PM 5/18/16
 */

namespace Youshido\Tests\Library\Relay;

use Youshido\GraphQL\Relay\Field\GlobalIdField;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;

class GlobalIdFieldTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleMethods(): void
    {
        $typeName = 'user';
        $field    = new GlobalIdField($typeName);
        $this->assertEquals('id', $field->getName());
        $this->assertEquals('The ID of an object', $field->getDescription());
        $this->assertEquals(new NonNullType(new IdType()), $field->getType());
    }
}
