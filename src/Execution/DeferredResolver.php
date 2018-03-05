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
 * This file is a part of GraphQL project.
 */

namespace Youshido\GraphQL\Execution;

/**
 * Default implementation of DeferredResolverInterface.
 */
class DeferredResolver implements DeferredResolverInterface
{
    /** @var callable */
    private $resolver;

    public function __construct($resolver)
    {
        $this->resolver = $resolver;
    }

    public function resolve()
    {
        return \call_user_func($this->resolver);
    }
}
