<?php

namespace Youshido\GraphQL\Type\Scalar;

use Youshido\GraphQL\Config\Traits\ConfigAwareTrait;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\TypeMap;

/**
 * Class AbstractScalarType
 */
abstract class AbstractScalarType extends AbstractType
{
    use ConfigAwareTrait;

    /**
     * @return string
     */
    public function getName()
    {
        $className = get_class($this);

        return substr($className, strrpos($className, '\\') + 1, -4);
    }

    /**
     * @return string
     */
    final public function getKind()
    {
        return TypeMap::KIND_SCALAR;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function parseValue($value)
    {
        return $this->serialize($value);
    }

    /**
     * @return bool
     */
    public function isInputType()
    {
        return true;
    }
}
