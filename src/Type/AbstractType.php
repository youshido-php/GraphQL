<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:05 AM
*/

namespace Youshido\GraphQL\Type;


use Youshido\GraphQL\Type\Config\Object\EnumTypeConfig;
use Youshido\GraphQL\Type\Config\Object\InputObjectTypeConfig;
use Youshido\GraphQL\Type\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Type\Config\Object\ObjectTypeConfig;

abstract class AbstractType implements TypeInterface
{

    /**
     * @var ObjectTypeConfig|InputObjectTypeConfig
     */
    protected $config;

    protected $isBuild = false;

    public function getDescription()
    {
        return $this->getConfig()->get('description', null);
    }

    /**
     * @return ObjectTypeConfig|InputObjectTypeConfig|EnumTypeConfig|InterfaceTypeConfig
     */
    public function getConfig()
    {
        $this->checkBuild();

        return $this->config;
    }

    public function isAbstractType()
    {
        return in_array($this->getKind(), [TypeMap::KIND_INTERFACE, TypeMap::KIND_UNION]);
    }

    public function isCompositeType()
    {
        return false;
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

    abstract public function checkBuild();

    public function parseValue($value)
    {
        return $this->serialize($value);
    }

    public function serialize($value)
    {
        return $value;
    }

    public function getType()
    {
        return $this->getConfig()->getType();
    }

    public function __toString()
    {
        return $this->getName();
    }
}
