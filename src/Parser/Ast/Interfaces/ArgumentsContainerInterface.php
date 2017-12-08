<?php

namespace Youshido\GraphQL\Parser\Ast\Interfaces;

use Youshido\GraphQL\Parser\Ast\Argument;

/**
 * Interface ArgumentsContainerInterface
 */
interface ArgumentsContainerInterface
{
    /**
     * @return Argument[]
     */
    public function getArguments();

    /**
     * @param string $name
     *
     * @return Argument
     */
    public function getArgument($name);
}
