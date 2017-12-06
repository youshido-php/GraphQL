<?php

namespace Youshido\GraphQL\Type\Object;

use Youshido\GraphQL\Type\AbstractType;

/**
 * Class AbstractMutationObjectType
 */
abstract class AbstractMutationObjectType extends AbstractObjectType
{
    /**
     * @return AbstractType
     */
    abstract public function getOutputType();

    /**
     * @return AbstractType
     */
    public function getType()
    {
        return $this->getOutputType();
    }
}
