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

namespace Youshido\GraphQL\Parser\Ast;

use Youshido\GraphQL\Parser\Ast\Interfaces\LocatableInterface;
use Youshido\GraphQL\Parser\Location;

abstract class AbstractAst implements LocatableInterface
{
    /** @var Location */
    private $location;

    public function __construct(Location $location)
    {
        $this->location = $location;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }
}
