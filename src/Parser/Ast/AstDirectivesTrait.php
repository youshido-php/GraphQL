<?php

namespace Youshido\GraphQL\Parser\Ast;

/**
 * Trait AstDirectivesTrait
 */
trait AstDirectivesTrait
{
    /** @var Directive[] */
    protected $directives;

    /** @var null|mixed */
    private $directivesCache;

    /**
     * @return bool
     */
    public function hasDirectives()
    {
        return (bool) count($this->directives);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasDirective($name)
    {
        return array_key_exists($name, $this->directives);
    }

    /**
     * @param $name
     *
     * @return null|Directive
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
    public function setDirectives(array $directives)
    {
        $this->directives      = [];
        $this->directivesCache = null;

        foreach ($directives as $directive) {
            $this->addDirective($directive);
        }
    }

    /**
     * @param Directive $directive
     */
    public function addDirective(Directive $directive)
    {
        $this->directives[$directive->getName()] = $directive;
    }
}
