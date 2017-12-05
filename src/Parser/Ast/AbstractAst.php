<?php

namespace Youshido\GraphQL\Parser\Ast;

use Youshido\GraphQL\Parser\Ast\Interfaces\LocatableInterface;
use Youshido\GraphQL\Parser\Location;

/**
 * Class AbstractAst
 */
abstract class AbstractAst implements LocatableInterface
{
    /** @var  Location */
    private $location;

    /**
     * AbstractAst constructor.
     *
     * @param Location $location
     */
    public function __construct(Location $location)
    {
        $this->location = $location;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation(Location $location)
    {
        $this->location = $location;
    }
}
