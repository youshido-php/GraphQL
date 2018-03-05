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
/**
 * Date: 13.05.16.
 */

namespace Youshido\Tests\DataProvider;

use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

class TestField extends AbstractField
{
    /**
     * @return AbstractObjectType
     */
    public function getType()
    {
        return new IntType();
    }

    public function resolve($value, array $args, ResolveInfo $info)
    {
        return $value;
    }

    public function getDescription()
    {
        return 'description';
    }
}
