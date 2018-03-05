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
 * Date: 23.11.15.
 */

namespace Youshido\GraphQL\Parser\Ast;

use Youshido\GraphQL\Parser\Ast\Interfaces\FieldInterface;
use Youshido\GraphQL\Parser\Location;

class Field extends AbstractAst implements FieldInterface
{
    use AstArgumentsTrait;
    use AstDirectivesTrait;

    /** @var string */
    private $name;

    /** @var string|null */
    private $alias;

    /**
     * @param string   $name
     * @param string   $alias
     * @param array    $arguments
     * @param array    $directives
     * @param Location $location
     */
    public function __construct($name, $alias, array $arguments, array $directives, Location $location)
    {
        parent::__construct($location);

        $this->name  = $name;
        $this->alias = $alias;
        $this->setArguments($arguments);
        $this->setDirectives($directives);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string|null $alias
     */
    public function setAlias($alias): void
    {
        $this->alias = $alias;
    }

    public function hasFields()
    {
        return false;
    }

    public function getFields()
    {
        return [];
    }
}
