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

trait AstDirectivesTrait
{
    /** @var Directive[] */
    protected $directives;

    private $directivesCache;

    public function hasDirectives()
    {
        return (bool) \count($this->directives);
    }

    public function hasDirective($name)
    {
        return \array_key_exists($name, $this->directives);
    }

    /**
     * @param $name
     *
     * @return Directive|null
     */
    public function getDirective($name)
    {
        $directive = null;

        if (isset($this->directives[$name])) {
            $directive = $this->directives[$name];
        }

        return $directive;
    }

    /**
     * @return Directive[]
     */
    public function getDirectives()
    {
        return $this->directives;
    }

    /**
     * @param $directives Directive[]
     */
    public function setDirectives(array $directives): void
    {
        $this->directives      = [];
        $this->directivesCache = null;

        foreach ($directives as $directive) {
            $this->addDirective($directive);
        }
    }

    public function addDirective(Directive $directive): void
    {
        $this->directives[$directive->getName()] = $directive;
    }
}
