<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:05 AM
*/

namespace Youshido\GraphQL\Type;


use Youshido\GraphQL\Config\Object\EnumTypeConfig;
use Youshido\GraphQL\Config\Object\InputObjectTypeConfig;
use Youshido\GraphQL\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Config\Traits\ConfigAwareTrait;
use Youshido\GraphQL\Config\TypeConfigInterface;

abstract class AbstractType implements TypeInterface
{

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

    public function getNullableType()
    {
        return $this;
    }

    public function parseValue($value)
    {
        return $value;
    }

    public function serialize($value)
    {
        return $value;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
