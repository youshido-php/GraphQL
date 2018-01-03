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

    /**
     * @param array|string      $argument
     * @param null|array|string $info
     *
     * @return $this
     */
    public function addArgument($argument, $info = null);

    /**
     * @return AbstractType[]
     */
    public function getArguments();

    /**
     * @param string $name
     *
     * @return InputFieldInterface
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
