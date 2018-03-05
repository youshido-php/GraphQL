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

interface FetcherInterface
{
    /**
     * Resolve node.
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
