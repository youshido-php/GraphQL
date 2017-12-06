<?php

namespace Youshido\GraphQL\Type\Scalar;

/**
 * Class IntType
 */
class IntType extends AbstractScalarType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Int';
    }

    /**
     * @param mixed $value
     *
     * @return int|null
     */
    public function serialize($value)
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        $value = (int) $value;

        return $value !== 0 ? $value : null;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidValue($value)
    {
        return null === $value || is_int($value);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'The `Int` scalar type represents non-fractional signed whole numeric ' .
            'values. Int can represent values between -(2^53 - 1) and 2^53 - 1 since ' .
            'represented in JSON as double-precision floating point numbers specified' .
            'by [IEEE 754](http://en.wikipedia.org/wiki/IEEE_floating_point).';
    }
}
