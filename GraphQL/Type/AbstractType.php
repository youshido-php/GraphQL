<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:05 AM
*/

namespace Youshido\GraphQL\Type;


use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\Config\ObjectTypeConfig;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

abstract class AbstractType implements TypeInterface
{

    /**
     * @var ObjectTypeConfig
     */
    protected $config;

    protected $name;

    /**
     * @return ObjectTypeConfig
     * @todo: refactor hierarchy
     */
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

    function getName()
    {
        if (!$this->name) {
            throw new ConfigurationException("Type has to have a name");
        }

        return $this->name;
    }

    abstract public function isValidValue($value);

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->config->getFields();
    }

    public function hasArgument($name)
    {
        return $this->config->hasArgument($name);
    }

    public function getArgument($name)
    {
        return $this->config->getArgument($name);
    }

    /**
     * @return Field[]
     */
    public function getArguments()
    {
        return $this->config->getArguments();
    }

    public function hasField($fieldName)
    {
        return $this->config->hasField($fieldName);
    }

    public function getField($fieldName)
    {
        return $this->config->getField($fieldName);
    }
}