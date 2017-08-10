<?php
/**
 * Date: 16.11.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Exception\Parser;


use Youshido\GraphQL\Exception\Interfaces\LocationableExceptionInterface;
use Youshido\GraphQL\Parser\Location;

abstract class AbstractParserError extends \Exception implements LocationableExceptionInterface
{

    /** @var Location */
    private $location;

    public function __construct($message, Location $location)
    {
        parent::__construct($message);

        $this->location = $location;
    }

    public function getLocation()
    {
        return $this->location;
    }
}
