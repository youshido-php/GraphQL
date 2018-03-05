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
 * created: 5/15/16 2:46 PM
 */

namespace Youshido\Tests\Library\Type;

use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\Tests\DataProvider\TestListType;

class ListTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testInline(): void
    {
        $listType = new ListType(new StringType());
        $this->assertEquals(new StringType(), $listType->getNamedType());
        $this->assertEquals(new StringType(), $listType->getTypeOf());
        $this->assertTrue($listType->isCompositeType());
        $this->assertTrue($listType->isValidValue(['Test', 'Value']));
        $this->assertFalse($listType->isValidValue('invalid value'));
    }

    public function testStandaloneClass(): void
    {
        $listType = new TestListType();
        $this->assertEquals(new StringType(), $listType->getNamedType());
    }

    public function testListOfInputsWithArguments(): void
    {
    }
}
