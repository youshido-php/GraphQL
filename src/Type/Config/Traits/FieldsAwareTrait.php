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
use Youshido\GraphQL\Type\Field\InputField;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\TypeService;
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
        $this->addFields($sourceFields);
    }

    /**
     * @param array $fieldsArray
     * @return $this
     */
    public function addFields($fieldsArray)
    {
        foreach ($fieldsArray as $fieldName => $fieldInfo) {
            if ($fieldInfo instanceof Field) {
                $this->fields[$fieldName] = $fieldInfo;
                continue;
            } elseif ($fieldInfo instanceof AbstractType) {
                $config    = [];
                $namedType = $fieldInfo->getNamedType();
                if ($fieldInfo->getConfig() && $fieldInfo->getConfig()->get('resolve')) {
                    $config['resolve'] = $fieldInfo->getConfig()->get('resolve');
                } elseif (empty($config['resolve']) && (method_exists($fieldInfo, 'resolve'))) {
                    $config['resolve'] = [$fieldInfo, 'resolve'];
                }
                if ($fieldInfo->getConfig() && $fieldInfo->getConfig()->hasArguments()) {
                    $config['args'] = $fieldInfo->getConfig()->getArguments();
                }
                $this->addField($fieldName, $namedType, $config);
            } else {
                $this->addField($fieldName, $fieldInfo['type'], $fieldInfo);
            }
        }
        return $this;
    }

    public function addField($name, $type, $config = [])
    {
        if (is_string($type)) {
            if (!TypeService::isScalarType($type)) {
                throw new ConfigurationException('You can\'t pass ' . $type . ' as a string type.');
            }

            $type = TypeFactory::getScalarType($type);
        } else {
            if (empty($config['resolve']) && (method_exists($type, 'resolve'))) {
                $config['resolve'] = [$type, 'resolve'];
            }
        }

        $config['name'] = $name;
        $config['type'] = $type;

        if (
            isset($this->contextObject)
            && method_exists($this->contextObject, 'getKind')
            && $this->contextObject->getKind() == TypeMap::KIND_INPUT_OBJECT
        ) {
            $field = new InputField($config);
        } else {
            $field = new Field($config);
        }


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

    public function hasFields()
    {
        return !empty($this->fields);
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
