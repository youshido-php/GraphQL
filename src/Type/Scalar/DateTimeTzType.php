<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\Scalar;

class DateTimeTzType extends AbstractScalarType
{
    private $format = 'D, d M Y H:i:s O';

    public function getName()
    {
        return 'DateTimeTz';
    }
    public function isValidValue($value)
    {
        if ((is_object($value) && $value instanceof \DateTimeInterface) || is_null($value)) {
            return true;
        } else if (is_string($value)) {
            $date = $this->createFromFormat($value);
        } else {
            $date = null;
        }

        return $date ? true : false;
    }

    public function serialize($value)
    {
        $date = null;

        if (is_string($value)) {
            $date = $this->createFromFormat($value);
        } elseif ($value instanceof \DateTimeInterface) {
            $date = $value;
        }

        return $date ? $date->format($this->format) : null;
    }

    public function parseValue($value)
    {
        if (is_string($value)) {
            $date = $this->createFromFormat($value);
        } elseif ($value instanceof \DateTimeInterface) {
            $date = $value;
        } else {
            $date = false;
        }

        return $date ?: null;
    }

    private function createFromFormat($value)
    {
        return \DateTime::createFromFormat($this->format, $value);
    }

    public function getDescription()
    {
        return 'Representation of date and time in "r" format';
    }

}
