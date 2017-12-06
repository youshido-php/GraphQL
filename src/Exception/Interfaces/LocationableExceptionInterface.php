<?php

namespace Youshido\GraphQL\Exception\Interfaces;

use Youshido\GraphQL\Parser\Location;

/**
 * Interface LocationableExceptionInterface
 */
interface LocationableExceptionInterface
{
    /**
     * @return Location
     */
    public function getLocation();
}
