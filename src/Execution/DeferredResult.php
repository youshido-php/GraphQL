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
 * Wrapper class for deferred resolvers during execution process.
 * Not part of the public API.
 *
 * @internal
 */
final class DeferredResult implements DeferredResolverInterface
{
    /** @var mixed */
    public $result;

    /** @var callable */
    protected $callback;

    /** @var \Youshido\GraphQL\Execution\DeferredResolver */
    private $resolver;

    public function __construct(DeferredResolverInterface $resolver, callable $callback)
    {
        $this->resolver = $resolver;
        $this->callback = $callback;
    }

    public function resolve(): void
    {
        $this->result = \call_user_func($this->callback, $this->resolver->resolve());
    }
}
