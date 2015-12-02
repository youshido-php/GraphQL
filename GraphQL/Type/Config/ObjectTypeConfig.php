<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:32 AM
*/

namespace Youshido\GraphQL\Type\Config;


use Youshido\GraphQL\Field;
use Youshido\GraphQL\Type\Config\Traits\FieldsAwareTrait;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

class ObjectTypeConfig extends Config
{

    use FieldsAwareTrait;

    protected $arguments = [];

    public function getArgument($name)
    {
        return $this->hasArgument($name) ? $this->arguments[$name] : null;
    }

    public function hasArgument($name)
    {
        return array_key_exists($name, $this->arguments);
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function addArgument($name, $type, $config = [])
    {
        if (!is_string($type) || !TypeMap::isInputType($type)) {
            throw new ConfigurationException('Argument input type ' . $type . ' is not supported');
        }

        $config['name'] = $name;
        $config['type'] = TypeMap::getScalarTypeObject($type);

        $this->arguments[$name] = new Field($config);

        return $this;
    }

    public function getResolveFunction()
    {
        return empty($this->data['resolve']) ? null : $this->data['resolve'];
    }

    public function getRules()
    {
        return [
            'name'      => ['type' => 'string', 'required' => true],
            'fields'    => [],
            'arguments' => [],
            'resolve'   => [],
        ];
    }

    protected function setupType()
    {
        $sourceFields = empty($this->data['fields']) ? [] : $this->data['fields'];
        foreach ($sourceFields as $fieldName => $fieldInfo) {
            if ($fieldInfo instanceof Field || $fieldInfo instanceof ObjectType) {
                $this->fields[$fieldName] = $fieldInfo;
                continue;
            };

            $this->addField($fieldName, $fieldInfo['type'], $fieldInfo);
        }

        $sourceArguments = empty($this->data['arguments']) ? [] : $this->data['arguments'];
        foreach ($sourceArguments as $argumentName => $argumentInfo) {
            $this->addArgument($argumentName, $argumentInfo['type'], $argumentInfo);
        }
    }

}