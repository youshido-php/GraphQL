<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 5:07 PM 5/14/16
 */

namespace Youshido\GraphQL\Type\Traits;


use Youshido\GraphQL\Config\Traits\ConfigAwareTrait;

trait FieldsAwareObjectTrait
{
    use ConfigAwareTrait;

    public function addFields($fieldsList)
    {
        return $this->getConfig()->addFields($fieldsList);
    }

    public function addField($field, $fieldInfo = null)
    {
        return $this->getConfig()->addField($field, $fieldInfo);
    }

    public function getFields()
    {
        return $this->getConfig()->getFields();
    }

    public function getField($fieldName)
    {
        return $this->getConfig()->getField($fieldName);
    }

    public function hasField($fieldName)
    {
        return $this->getConfig()->hasField($fieldName);
    }

    public function hasFields()
    {
        return $this->getConfig()->hasFields();
    }

}
