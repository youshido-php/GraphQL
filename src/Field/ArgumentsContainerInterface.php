<?php

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Type\AbstractType;

/**
 * Interface ArgumentsContainerInterface
 */
interface ArgumentsContainerInterface
{
    /**
     * @param AbstractType[] $arguments
     */
    public function addArguments($arguments);

    /**
     * @param $name
     */
    public function removeArgument($name);

    public function addArgument($argument, $ArgumentInfo = null);

    /**
     * @return AbstractType[]
     */
    public function getArguments();

    /**
     * @param string $name
     *
     * @return AbstractType
     */
    public function getArgument($name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasArgument($name);

    /**
     * @return boolean
     */
    public function hasArguments();
}
