<?php

namespace Youshido\GraphQL\Execution;

/**
 * Default implementation of DeferredResolverInterface
 */
class DeferredResolver implements DeferredResolverInterface
{
    /** @var callable */
    private $resolver;

    /**
     * DeferredResolver constructor.
     *
     * @param callable $resolver
     */
    public function __construct($resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @return mixed
     */
    public function resolve()
    {
        return call_user_func($this->resolver);
    }
}
