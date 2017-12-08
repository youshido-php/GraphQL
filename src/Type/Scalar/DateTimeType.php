<?php

namespace Youshido\GraphQL\Type\Scalar;

/**
 * Class DateTimeType
 */
class DateTimeType extends AbstractScalarType
{
    /** @var string */
    private $format;

    /**
     * DateTimeType constructor.
     *
     * @param string $format
     */
    public function __construct($format = 'Y-m-d H:i:s')
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'DateTime';
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidValue($value)
    {
        if ((is_object($value) && $value instanceof \DateTimeInterface) || null === $value) {
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
        return sprintf('Representation of date and time in "%s" format', $this->format);
    }

    private function createFromFormat($value)
    {
        return \DateTime::createFromFormat($this->format, $value);
    }
}
