<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:05 AM
*/

namespace Youshido\GraphQL\Type;


use Youshido\GraphQL\Type\Config\Object\InputObjectTypeConfig;
use Youshido\GraphQL\Type\Config\Object\ObjectTypeConfig;

abstract class AbstractType implements TypeInterface
{

    /**
     * @var ObjectTypeConfig|InputObjectTypeConfig
     */
    protected $config;

    protected $name;

    /**
     * @return ObjectTypeConfig|InputObjectTypeConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    function getDescription()
    {
        return "";
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