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
 * created: 5/20/16 12:41 AM
 */

namespace Youshido\Tests\DataProvider;

use Youshido\GraphQL\Execution\Context\ExecutionContext;
use Youshido\GraphQL\Execution\ResolveInfo;

class TestResolveInfo
{
    public static function createTestResolveInfo($field = null)
    {
        if (empty($field)) {
            $field = new TestField();
        }

        return new ResolveInfo($field, [], new ExecutionContext(new TestSchema()));
    }
}
