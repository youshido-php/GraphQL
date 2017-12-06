<?php

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Type\AbstractType;

/**
 * Interface InputFieldInterface
 */
interface InputFieldInterface extends ArgumentsContainerInterface
{
    /**
     * @return AbstractType
     */
    public function getType();

    /**
     * @return string
     */
    public function getName();
}
