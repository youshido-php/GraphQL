<?php

namespace Youshido\GraphQL\Parser\Ast\Interfaces;

use Youshido\GraphQL\Parser\Ast\Directive;

/**
 * Interface DirectivesContainerInterface
 */
interface DirectivesContainerInterface
{
    /**
     * @return bool
     */
    public function hasDirectives();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasDirective($name);

    /**
     * @param $name
     *
     * @return null|Directive
     */
    public function getDirective($name);

    /**
     * @return Directive[]
     */
    public function getDirectives();

    /**
     * @param $directives Directive[]
     */
    public function setDirectives(array $directives);

    /**
     * @param Directive $directive
     */
    public function addDirective(Directive $directive);
}
