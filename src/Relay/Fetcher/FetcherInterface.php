<?php

namespace Youshido\GraphQL\Relay\Fetcher;

/**
 * Interface FetcherInterface
 */
interface FetcherInterface
{
    /**
     * Resolve node
     *
     * @param $type string
     * @param $id   string
     *
     * @return mixed
     */
    public function resolveNode($type, $id);

    /**
     * @param $object
     *
     * @return mixed
     */
    public function resolveType($object);
}
