<?php

namespace Youshido\GraphQL\Execution;

/**
 * Wrapper class for deferred resolvers during execution process.
 * Not part of the public API.
 *
 * @internal
 */
class DeferredResult implements DeferredResolverInterface
{
    /** @var \Youshido\GraphQL\Execution\DeferredResolver */
    private $resolver;

    /** @var callable */
    protected $callback;

    /** @var  mixed */
    public $result;

    /**
     * DeferredResult constructor.
     *
     * @param DeferredResolverInterface $resolver
     * @param callable                  $callback
     */
    public function __construct(DeferredResolverInterface $resolver, callable $callback)
    {
        $this->resolver = $resolver;
        $this->callback = $callback;
    }

    /**
     * Resolve deferred result
     */
    public function resolve()
    {
        $this->result = call_user_func($this->callback, $this->resolver->resolve());
    }
}
