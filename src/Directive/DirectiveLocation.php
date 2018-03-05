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
 * Date: 3/24/17.
 */

namespace Youshido\GraphQL\Directive;

class DirectiveLocation
{
    public const QUERY = 'QUERY';

    public const MUTATION = 'MUTATION';

    public const FIELD = 'FIELD';

    public const FRAGMENT_DEFINITION = 'FRAGMENT_DEFINITION';

    public const FRAGMENT_SPREAD = 'FRAGMENT_SPREAD';

    public const INLINE_FRAGMENT = 'INLINE_FRAGMENT';
}
