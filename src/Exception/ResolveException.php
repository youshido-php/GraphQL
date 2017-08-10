<?php
/**
 * Date: 26.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Exception;


use Youshido\GraphQL\Exception\Interfaces\LocationableExceptionInterface;
use Youshido\GraphQL\Parser\Location;

class ResolveException extends \Exception implements LocationableExceptionInterface
{

    /** @var  Location */
    private $location;

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
