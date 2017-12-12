<?php

namespace Youshido\GraphQL\Config\Traits;

use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Field\InputFieldInterface;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;

/**
 * Class FieldsAwareTrait
 *
 * todo: this trait is used in input object to, but create Field, must InputField
 */
trait FieldsAwareConfigTrait
{
    protected $fields = []; //todo: think about this: build fields in $fields and not build in config

    /**
     * Configure object fields
     */
    public function buildFields()
    {
        if (!empty($this->data['fields'])) {
            $this->addFields($this->data['fields']);
        }
    }

    /**
     * Add fields from passed interface
     *
     * @param AbstractInterfaceType $interfaceType
     *
     * @return $this
     */
    public function applyInterface(AbstractInterfaceType $interfaceType)
    {
        $this->addFields($interfaceType->getFields());

        return $this;
    }

    /**
     * @param array $fieldsList
     *
     * @return $this
     */
    public function addFields($fieldsList)
    {
        foreach ($fieldsList as $fieldName => $fieldConfig) {
            if ($fieldConfig instanceof FieldInterface) {
                $this->fields[$fieldConfig->getName()] = $fieldConfig;
                continue;
            }

            if ($fieldConfig instanceof InputFieldInterface) {
                $this->fields[$fieldConfig->getName()] = $fieldConfig;
                continue;
            }

            $this->addField($fieldName, $this->buildFieldConfig($fieldName, $fieldConfig));
        }

        return $this;
    }

    /**
     * @param FieldInterface|string $field Field name or Field Object
     * @param mixed                 $info  Field Type or Field Config array
     *
     * @return $this
     */
    public function addField($field, $info = null)
    {
        if (!($field instanceof FieldInterface)) {
            $field = new Field($this->buildFieldConfig($field, $info));
        }

        $this->fields[$field->getName()] = $field;

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
     * @return bool
     */
    public function hasFields()
    {
        return !empty($this->fields);
    }

    /**
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function removeField($name)
    {
        if ($this->hasField($name)) {
            unset($this->fields[$name]);
        }

        return $this;
    }

    protected function buildFieldConfig($name, $info = null)
    {
        if (!is_array($info)) {
            $info = ['type' => $info, 'name' => $name,];
        } elseif (empty($info['name'])) {
            $info['name'] = $name;
        }

        return $info;
    }
}
