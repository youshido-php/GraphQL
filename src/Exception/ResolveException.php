<?php

namespace Youshido\GraphQL\Exception;

use Youshido\GraphQL\Exception\Interfaces\LocationableExceptionInterface;
use Youshido\GraphQL\Parser\Location;

/**
 * Class ResolveException
 */
class ResolveException extends GraphQLException implements LocationableExceptionInterface
{
    /** @var  Location */
    private $location;

    /**
     * ResolveException constructor.
     *
     * @param string        $message
     * @param Location|null $location
     */
    public function __construct($message, Location $location = null)
    {
        parent::__construct($message);

        $this->location = $location;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }
}
