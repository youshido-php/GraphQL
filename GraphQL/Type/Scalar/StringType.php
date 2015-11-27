<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:05 AM
*/

namespace Youshido\GraphQL\Type\Scalar;


class StringType extends AbstractScalarType
{
    public function parseValue($value)
    {
        return $this->getStringValue($value);
    }

    public function serialize($value)
    {
        return $this->getStringValue($value);
    }

    /**
     * @param $value
     * @return string
     */
    public function getStringValue($value)
    {
        if ($value === true) {
            return 'true';
        }
        if ($value === false) {
            return 'false';
        }
        return (string)$value;
    }


}