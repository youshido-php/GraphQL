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
 * Date: 3/17/17.
 */

namespace Youshido\GraphQL\Parser\Ast;

use Youshido\GraphQL\Parser\Location;

class Directive extends AbstractAst
{
    use AstArgumentsTrait;

    /** @var string */
    private $name;

    /**
     * @param string   $name
     * @param array    $arguments
     * @param Location $location
     */
    public function __construct($name, array $arguments, Location $location)
    {
        parent::__construct($location);

        $this->name = $name;
        $this->setArguments($arguments);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }
}
