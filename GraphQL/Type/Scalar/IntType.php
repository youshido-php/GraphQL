<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\Scalar;


class IntType extends AbstractScalarType
{

    public function getDescription()
    {
        return  'The `Int` scalar type represents non-fractional signed whole numeric ' .
                'values. Int can represent values between -(2^53 - 1) and 2^53 - 1 since ' .
                'represented in JSON as double-precision floating point numbers specified' .
                'by [IEEE 754](http://en.wikipedia.org/wiki/IEEE_floating_point).';
    }

    public function parseValue($value, $errorList = null)
    {
        if (!is_integer($value)) {
//            $errorList->addError(new ValidationException(
//                                     sprintf('Argument \"%s\" expected type \"Int\"', $name)
//                                 ));
            return null;
        }
        return $value;
        /**
         * { user(id: 1) }
         */
    }

    public function serialize($value)
    {
        return (int)$value;
    }


}