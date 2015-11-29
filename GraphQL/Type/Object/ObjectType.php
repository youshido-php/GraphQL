<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:24 AM
*/

namespace Youshido\GraphQL\Type\Object;


use Youshido\GraphQL\Field;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Config\ObjectTypeConfig;

class ObjectType extends AbstractType
{

    protected $name = "Object";

    protected $fields = [];

    /**
     * ObjectType constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = new ObjectTypeConfig($config, $this);

        $this->fields = $this->config->getFields();
        $this->name   = $this->config->getName();
    }

    public function parseValue($value)
    {
        return $value;
    }

    public function serialize($value)
    {
        return $value;
    }

    public function resolve($args = [])
    {
        $callable = $this->config->getResolveFunction();
        return is_callable($callable) ? $callable($args) : null;
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function hasField($fieldName)
    {
        return array_key_exists($fieldName, $this->fields);
    }

    public function getField($fieldName)
    {
        return $this->hasField($fieldName) ? $this->fields[$fieldName] : null;
    }

    public function isValidValue($value)
    {
        return (get_class($value) == get_class($this));
    }

}