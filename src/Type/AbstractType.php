<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:05 AM
*/

namespace Youshido\GraphQL\Type;


use Youshido\GraphQL\Type\Object\AbstractObjectType;

abstract class AbstractType implements TypeInterface
{

    protected $lastValidationError = null;

    public function isCompositeType()
    {
        return false;
    }

    /**
     * @return AbstractType
     */
    public function getType()
    {
        return $this;
    }

    /**
     * @return AbstractType
     */
    public function getNamedType()
    {
        return $this->getType();
    }

    /**
     * @return AbstractType|AbstractObjectType
     */
    public function getNullableType()
    {
        return $this;
    }

    public function getValidationError($value = null)
    {
        return $this->lastValidationError;
    }

    public function isValidValue($value)
    {
        return true;
    }

    public function parseValue($value)
    {
        return $value;
    }

    public function parseInputValue($value)
    {
        return $this->parseValue($value);
    }

    public function serialize($value)
    {
        return $value;
    }

    public function isInputType()
    {
        return false;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
