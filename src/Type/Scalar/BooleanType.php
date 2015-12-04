<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\Scalar;


class BooleanType extends AbstractScalarType
{

    public function parseValue($value)
    {
        return $this->getBooleanValue($value);
    }

    public function serialize($value)
    {
        return $this->getBooleanValue($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function getBooleanValue($value)
    {
        if ($value === 'true') {
            return true;
        }
        if ($value === 'false') {
            return false;
        }

        return (bool)$value;
    }


    public function isValidValue($value)
    {
        return is_bool($value);
    }

    public function getDescription()
    {
        return 'The `Boolean` scalar type represents `true` or `false`.';
    }

}