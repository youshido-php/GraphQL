<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\Scalar;


class DateTimeType extends AbstractScalarType
{

    /**
     * @param $value \DateTime
     * @return null|string
     */
    public function serialize($value)
    {
        if ($value === null) {
            return null;
        }

        return $value->format('Y-m-d H:i:s');
    }

    public function isValidValue($value)
    {
        if (is_object($value)) {
            return true;
        }

        $d = \DateTime::createFromFormat('Y-m-d H:i:s', $value);

        return $d && $d->format('Y-m-d H:i:s') == $value;
    }

    public function getDescription()
    {
        return 'Representation of date and time in "Y-m-d H:i:s" format';
    }

    public function getName()
    {
        return 'DateTime';
    }


}