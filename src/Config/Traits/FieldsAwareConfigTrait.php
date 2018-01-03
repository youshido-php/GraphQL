<?php

namespace Youshido\GraphQL\Config\Traits;

use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Field\FieldInterface;

/**
 * Class FieldsAwareTrait
 */
trait FieldsAwareConfigTrait
{
    /**
     * @param array $fields
     *
     * @return $this
     */
    public function addFields(array $fields)
    {
        foreach ($fields as $fieldName => $fieldConfig) {
            if ($this->isFieldInstance($fieldConfig)) {
                $this->data['fields'][$fieldConfig->getName()] = $fieldConfig;
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
        if (!$this->isFieldInstance($field)) {
            $field = $this->createField($field, $info);
        }

        $this->data['fields'][$field->getName()] = $field;

        return $this;
    }

    /**
     * @param $name
     *
     * @return Field
     */
    public function getField($name)
    {
        return $this->hasField($name) ? $this->data['fields'][$name] : null;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasField($name)
    {
        return isset($this->data['fields'][$name]);
    }

    /**
     * @return bool
     */
    public function hasFields()
    {
        return !empty($this->data['fields']);
    }

    /**
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return $this->data['fields'];
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function removeField($name)
    {
        if ($this->hasField($name)) {
            unset($this->data['fields'][$name]);
        }

        return $this;
    }

    protected function isFieldInstance($field)
    {
        return $field instanceof FieldInterface;
    }

    protected function createField($field, $info = null)
    {
        return new Field($this->buildFieldConfig($field, $info));
    }

    /**
     * @param string|array $name
     * @param null|array   $info
     *
     * @return array|null
     */
    protected function buildFieldConfig($name, $info = null)
    {
        if (!is_array($info)) {
            $info = ['type' => $info, 'name' => $name,];
        } elseif (empty($info['name'])) {
            $info['name'] = $name;
        }

        return $info;
    }

    private function buildFields()
    {
        if (!empty($this->data['fields'])) {
            $fields               = $this->data['fields'];
            $this->data['fields'] = [];

            $this->addFields($fields);
        }
    }
}
