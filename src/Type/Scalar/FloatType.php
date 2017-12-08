<?php

namespace Youshido\GraphQL\Type\Scalar;

/**
 * Class FloatType
 */
class FloatType extends AbstractScalarType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Float';
    }

    /**
     * @param mixed $value
     *
     * @return float|null
     */
    public function serialize($value)
    {
        if ($value === null) {
            return null;
        }

        return (float) $value;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidValue($value)
    {
        return null === $value || is_float($value) || is_int($value);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'The `Float` scalar type represents signed double-precision fractional ' .
            'values as specified by ' .
            '[IEEE 754](http://en.wikipedia.org/wiki/IEEE_floating_point).';
    }
}
