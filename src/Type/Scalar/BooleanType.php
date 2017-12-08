<?php

namespace Youshido\GraphQL\Type\Scalar;

/**
 * Class BooleanType
 */
class BooleanType extends AbstractScalarType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Boolean';
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function serialize($value)
    {
        if ($value === 'true') {
            return true;
        }
        if ($value === 'false') {
            return false;
        }

        return (bool) $value;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidValue($value)
    {
        return null === $value || is_bool($value);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'The `Boolean` scalar type represents `true` or `false`.';
    }
}
