<?php

namespace Youshido\GraphQL\Parser\Ast\Interfaces;

use Youshido\GraphQL\Parser\Location;

/**
 * Interface LocatableInterface
 */
interface LocatableInterface
{
    /**
     * @return Location
     */
    public function getLocation();
}
