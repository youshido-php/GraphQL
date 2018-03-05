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
 * created: 5/19/16 1:28 PM
 */

namespace Youshido\Tests\Schema;

use Youshido\GraphQL\Execution\Context\ExecutionContext;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Parser\Ast\Field as FieldAST;
use Youshido\GraphQL\Parser\Location;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\Tests\DataProvider\TestSchema;

class ResolveInfoTest extends \PHPUnit_Framework_TestCase
{
    public function testMethods(): void
    {
        $fieldAst         = new FieldAST('name', null, [], [], new Location(1, 1));
        $field            = new Field(['name' => 'id', 'type' => new IntType()]);
        $returnType       = new IntType();
        $executionContext = new ExecutionContext(new TestSchema());
        $info             = new ResolveInfo($field, [$fieldAst], $executionContext);

        $this->assertEquals($field, $info->getField());
        $this->assertEquals([$fieldAst], $info->getFieldASTList());
        $this->assertEquals($returnType, $info->getReturnType());
        $this->assertEquals($executionContext, $info->getExecutionContext());
    }
}
