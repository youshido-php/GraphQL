<?php

namespace Youshido\GraphQL\Exception\Parser;

use Youshido\GraphQL\Exception\GraphQLException;
use Youshido\GraphQL\Exception\Interfaces\LocationableExceptionInterface;
use Youshido\GraphQL\Parser\Location;

/**
 * Class AbstractParserException
 */
abstract class AbstractParserException extends GraphQLException implements LocationableExceptionInterface
{
    /** @var Location */
    private $location;

    /**
     * AbstractParserException constructor.
     *
     * @param string   $message
     * @param Location $location
     */
    public function __construct($message, Location $location)
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
