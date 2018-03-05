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
 * Date: 16.11.16.
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
