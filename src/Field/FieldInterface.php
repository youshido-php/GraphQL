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
 * Date: 17.05.16.
 */

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Execution\ResolveInfo;

interface FieldInterface extends InputFieldInterface
{
    public function resolve($value, array $args, ResolveInfo $info);

    public function getResolveFunction();
}
