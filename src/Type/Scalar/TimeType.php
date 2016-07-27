<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 27/07/2016 01:36 PM
*/

namespace Youshido\GraphQL\Type\Scalar;


class DateType extends AbstractScalarType
{

    public function getName()
    {
        return 'Time';
    }

    /**
     * @param $value \DateTime
     * @return null|string
     */
    public function serialize($value)
    {
        if ($value === null) {
            return null;
        }

        return $value->format('H:i:s');
    }

    public function isValidValue($value)
    {
        if (is_object($value)) {
            return true;
        }

        $d = \DateTime::createFromFormat('H:i:s', $value);

        return $d && $d->format('H:i:s') == $value;
    }

    public function getDescription()
    {
        return 'Representation of time in "H:i:s" format';
    }

}
