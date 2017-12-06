<?php

namespace Youshido\GraphQL\Execution;

/**
 * Interface definition for deferred resolvers.
 *
 * Fields may return a value implementing this interface to use deferred
 * resolving to optimize query performance.
 */
interface DeferredResolverInterface
{
    /**
     * @return mixed
     *   The actual result value.
     */
    public function resolve();
}
