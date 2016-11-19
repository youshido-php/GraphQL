<?php
/**
 * Date: 16.11.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast\Interfaces;


use Youshido\GraphQL\Parser\Location;

interface LocatableInterface
{

    /**
     * @return Location
     */
    public function getLocation();

}