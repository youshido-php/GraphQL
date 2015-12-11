<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 11:05 PM
*/

namespace Youshido\GraphQL\Type\Config\Traits;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

/**
 * Class FieldsAwareTrait
 * @package Youshido\GraphQL\Type\Config\Traits
 */
trait FieldsAwareTrait
{
    protected $fields = [];

    public function buildFields()
    {
        $sourceFields = empty($this->data['fields']) ? [] : $this->data['fields'];
        foreach ($sourceFields as $fieldName => $fieldInfo) {
            if ($fieldInfo instanceof Field || $fieldInfo instanceof AbstractType) {
                $this->fields[$fieldName] = $fieldInfo;
                continue;
            };

            $this->addField($fieldName, $fieldInfo['type'], $fieldInfo);
        }
    }

    /**
     * @param array $fieldsArray
     */
    public function addFields($fieldsArray)
    {
        foreach($fieldsArray as $fieldName => $fieldConfig) {
            if (is_object($fieldConfig)) {
                $this->addField($fieldName, $fieldConfig);
            } else {
                $this->addField($fieldName, $fieldConfig['type'], $fieldConfig);
            }
        }
    }

    public function addField($name, $type, $config = [])
    {
        if (is_string($type)) {
            if (!TypeMap::isScalarType($type)) {
                throw new ConfigurationException('You can\'t pass ' . $type . ' as a string type.');
            }

            $type = TypeMap::getScalarTypeObject($type);
        }

        $config['name'] = $name;
        $config['type'] = $type;
        $field          = new Field($config);

        $this->fields[$name] = $field;

        return $this;
    }

    /**
     * @param $name
     *
     * @return Field
     */
    public function getField($name)
    {
        return $this->hasField($name) ? $this->fields[$name] : null;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasField($name)
    {
        return array_key_exists($name, $this->fields);
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function removeField($name)
    {
        if ($this->hasField($name)) {
            unset($this->fields[$name]);
        }
    }
}