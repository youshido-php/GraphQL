<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:05 AM
*/

namespace Youshido\GraphQL\Type;


use Youshido\GraphQL\Type\Config\Config;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

abstract class AbstractType implements TypeInterface
{

    /**
     * @var Config
     */
    protected $config;

    protected $name;

    function getName()
    {
        if (!$this->name) {
            throw new ConfigurationException("Type has to have a name");
        }

        return $this->name;
    }

    public function getConfig()
    {
        return $this->config;
    }

    function getDescription()
    {
        return "This Type doesn't have a description yet";
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

    abstract public function isValidValue($value);

}