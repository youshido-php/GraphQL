<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:32 AM
*/

namespace Youshido\GraphQL\Type\Config;


use Youshido\GraphQL\Field;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\TypeKind;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

class ObjectTypeConfig extends Config
{

    protected $fields = [];

    protected $arguments = [];

    public function getName()
    {
        return $this->data['name'];
    }

    public function getField($name)
    {
        return $this->hasField($name) ? $this->fields[$name] : null;
    }

    public function hasField($name)
    {
        return array_key_exists($name, $this->fields);
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getArgument($name)
    {
        return $this->hasArgument($name) ? $this->fields[$name] : null;
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
        if (!is_string($type) || !TypeMap::isTypeAllowed($type)) {
            throw new ConfigurationException('Argument input type ' . $type . ' is not supported');
        }

        $config['name'] = $name;
        $config['type'] = TypeMap::getTypeObject($type);

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

    public function addField($name, $type, $config = [])
    {
        if (is_string($type)) {
            if (!TypeMap::isTypeAllowed($type)) {
                throw new ConfigurationException('You can\'t pass ' . $type . ' as a string type.');
            }

            $type = TypeMap::getTypeObject($type);
        }

        if ($type->getKind() == TypeKind::KIND_SCALAR) {
            $config['name'] = $name;
            $config['type'] = $type;
            $field          = new Field($config);
        } else {
            $field = $type;
        }

        $this->fields[$name] = $field;

        return $this;
    }


}