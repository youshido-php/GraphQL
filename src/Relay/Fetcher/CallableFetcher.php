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

namespace Youshido\GraphQL\Relay\Fetcher;

class CallableFetcher implements FetcherInterface
{
    /** @var callable */
    protected $resolveNodeCallable;

    /** @var callable */
    protected $resolveTypeCallable;

    public function __construct(callable $resolveNode, callable $resolveType)
    {
        $this->resolveNodeCallable = $resolveNode;
        $this->resolveTypeCallable = $resolveType;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveNode($type, $id)
    {
        $callable = $this->resolveNodeCallable;

        return $callable($type, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveType($object)
    {
        $callable = $this->resolveTypeCallable;

        return $callable($object);
    }
}
