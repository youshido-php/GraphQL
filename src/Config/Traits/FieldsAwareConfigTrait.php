<?php
/**
 * Copyright (c) 2015–2018 Alexandr Viniychuk <http://youshido.com>.
 * Copyright (c) 2015–2018 Portey Vasil <https://github.com/portey>.
 * Copyright (c) 2018 Ryan Parman <https://github.com/skyzyx>.
 * Copyright (c) 2018 Ashley Hutson <https://github.com/asheliahut>.
 * Copyright (c) 2015–2018 Contributors.
 *
 * http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
/*
 * This file is a part of graphql-youshido project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 12/1/15 11:05 PM
 */

namespace Youshido\GraphQL\Config\Traits;

use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Field\InputFieldInterface;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;

/**
 * Class FieldsAwareTrait.
 */
trait FieldsAwareConfigTrait
{
    protected $fields = [];

    public function buildFields(): void
    {
        if (!empty($this->data['fields'])) {
            $this->addFields($this->data['fields']);
        }
    }

    /**
     * Add fields from passed interface.
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
     * @param FieldInterface|string $field     Field name or Field Object
     * @param mixed                 $fieldInfo Field Type or Field Config array
     *
     * @return $this
     */
    public function addField($field, $fieldInfo = null)
    {
        if (!($field instanceof FieldInterface)) {
            $field = new Field($this->buildFieldConfig($field, $fieldInfo));
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
        return \array_key_exists($name, $this->fields);
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

        return $this;
    }

    protected function buildFieldConfig($name, $info = null)
    {
        if (!\is_array($info)) {
            $info = [
                'type' => $info,
                'name' => $name,
            ];
        } elseif (empty($info['name'])) {
            $info['name'] = $name;
        }

        return $info;
    }
}
