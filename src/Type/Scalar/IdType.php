<?php

namespace Youshido\GraphQL\Type\Scalar;

/**
 * Class IdType
 */
class IdType extends AbstractScalarType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'ID';
    }

    /**
     * @param mixed $value
     *
     * @return null|string
     */
    public function serialize($value)
    {
        if (null === $value) {
            return null;
        }

        return (string) $value;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'The `ID` scalar type represents a unique identifier, often used to ' .
            'refetch an object or as key for a cache. The ID type appears in a JSON ' .
            'response as a String; however, it is not intended to be human-readable. ' .
            'When expected as an input type, any string (such as `"4"`) or integer ' .
            '(such as `4`) input value will be accepted as an ID.';
    }
}
