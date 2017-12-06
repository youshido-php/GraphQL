<?php

namespace Youshido\GraphQL\Parser\Ast\Interfaces;

/**
 * Interface FieldInterface
 */
interface FieldInterface extends LocatableInterface, DirectivesContainerInterface, ArgumentsContainerInterface
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
     * @return bool
     */
    public function hasFields();

    /**
     * @return FieldInterface[]
     */
    public function getFields();
}
