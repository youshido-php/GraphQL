<?php

namespace Youshido\GraphQL\Execution;

/**
 * Provides a factory for resolvers.
 *
 * For some advanced usecases one might not actually want an anonymous function
 * for the resolver, like for caching purposes. This function allows you to
 * just provide the string and create the resolver on runtime.
 */
interface ResolveStringResolverInterface
{
    /**
     * Returns the resolve function.
     *
     * @param string $resolveString
     *   The passed in factory, maybe for example service_name:get.
     *
     * @return callable
     */
    public function resolve($resolveString);

}
