<?php

namespace Youshido\GraphQL\Directive;

use Youshido\GraphQL\Field\ArgumentsContainerInterface;

/**
 * Interface DirectiveInterface
 */
interface DirectiveInterface extends ArgumentsContainerInterface
{
    /**
     * @return string
     */
    public function getName();
}
