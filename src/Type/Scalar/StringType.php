<?php

namespace Youshido\GraphQL\Type\Scalar;

/**
 * Class StringType
 */
class StringType extends AbstractScalarType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'String';
    }

    /**
     * @param mixed $value
     *
     * @return null|string
     */
    public function serialize($value)
    {
        if ($value === true) {
            return 'true';
        }
        if ($value === false) {
            return 'false';
        }
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return '';
        }

        return (string) $value;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidValue($value)
    {
        return null === $value || is_scalar($value) || (is_object($value) && method_exists($value, '__toString'));
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'The `String` scalar type represents textual data, represented as UTF-8 ' .
            'character sequences. The String type is most often used by GraphQL to ' .
            'represent free-form human-readable text.';
    }
}
