<?php

namespace Youshido\GraphQL\Type\Scalar;

/**
 * Class DateTimeTzType
 */
class DateTimeTzType extends AbstractScalarType
{
    /** @var string */
    private $format = 'D, d M Y H:i:s O';

    /**
     * @return string
     */
    public function getName()
    {
        return 'DateTimeTz';
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidValue($value)
    {
        if (null === $value || (is_object($value) && $value instanceof \DateTimeInterface)) {
            return true;
        }

        $date = null;
        if (is_string($value)) {
            $date = $this->createFromFormat($value);
        }

        return $date ? true : false;
    }

    /**
     * @param mixed $value
     *
     * @return null|string
     */
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

    /**
     * @param mixed $value
     *
     * @return null
     */
    public function parseValue($value)
    {
        $date = null;

        if (is_string($value)) {
            $date = $this->createFromFormat($value);
        } elseif ($value instanceof \DateTimeInterface) {
            $date = $value;
        }

        return $date ?: null;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Representation of date and time in "r" format';
    }

    private function createFromFormat($value)
    {
        return \DateTime::createFromFormat($this->format, $value);
    }
}
