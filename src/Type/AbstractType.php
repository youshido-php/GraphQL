<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
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
    protected $lastValidationError;

    public function __toString()
    {
        return $this->getName();
    }

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
     * @return AbstractObjectType|AbstractType
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
}
