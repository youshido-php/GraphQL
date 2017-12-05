<?php

namespace Youshido\GraphQL\Parser\Ast\Interfaces;

use Youshido\GraphQL\Parser\Ast\Argument;

/**
 * Interface FieldInterface
 */
interface FieldInterface extends LocatableInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getAlias();

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

    /**
     * @return bool
     */
    public function hasFields();

    /**
     * @return array
     */
    public function getFields();
}
