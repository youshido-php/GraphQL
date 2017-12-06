<?php

namespace Youshido\GraphQL\Relay\Fetcher;

/**
 * Class CallableFetcher
 */
class CallableFetcher implements FetcherInterface
{
    /** @var  callable */
    protected $resolveNodeCallable;

    /** @var  callable */
    protected $resolveTypeCallable;

    /**
     * CallableFetcher constructor.
     *
     * @param callable $resolveNode
     * @param callable $resolveType
     */
    public function __construct(callable $resolveNode, callable $resolveType)
    {
        $this->resolveNodeCallable = $resolveNode;
        $this->resolveTypeCallable = $resolveType;
    }

    /**
     * @inheritdoc
     */
    public function resolveNode($type, $id)
    {
        $callable = $this->resolveNodeCallable;

        return $callable($type, $id);
    }

    /**
     * @inheritdoc
     */
    public function resolveType($object)
    {
        $callable = $this->resolveTypeCallable;

        return $callable($object);
    }
}
