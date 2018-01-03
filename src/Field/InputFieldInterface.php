<?php

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Type\AbstractType;

/**
 * Interface InputFieldInterface
 */
interface InputFieldInterface
{
    /**
     * @return AbstractType
     */
    public function getType();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getDefaultValue();
}
