<?php

namespace Youshido\GraphQL\Type\Traits;

/**
 * Trait FieldsAwareObjectTrait
 */
trait FieldsAwareObjectTrait
{
    use ConfigAwareTrait; //todo refactor

    public function addFields($fieldsList)
    {
        $this->getConfig()->addFields($fieldsList);

        return $this;
    }

    public function addField($field, $info = null)
    {
        $this->getConfig()->addField($field, $info);

        return $this;
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
