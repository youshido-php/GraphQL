<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 4/30/16 9:11 PM
*/

namespace Youshido\GraphQL\Validator\SchemaValidator;


use Youshido\GraphQL\AbstractSchema;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerInterface;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;
use Youshido\GraphQL\Validator\Exception\ConfigurationException;

class SchemaValidator implements ErrorContainerInterface
{
    use ErrorContainerTrait;

    public function validate(AbstractSchema $schema)
    {
        try {
            foreach ($schema->getQueryType()->getConfig()->getFields() as $field) {
                if ($field->getType() instanceof AbstractObjectType) {
                    $this->assertInterfaceImplementationCorrect($field->getType());
                }
            }
        } catch (\Exception $e) {
            $this->addError($e);

            return false;
        }

        return true;
    }

    protected function assertInterfaceImplementationCorrect(AbstractObjectType $type)
    {
        if (!$type->getInterfaces()) return true;

        foreach ($type->getInterfaces() as $interface) {
            foreach ($interface->getConfig()->getFields() as $intField) {
                $this->assertFieldsIdentical($intField, $type->getConfig()->getField($intField->getName()), $interface);
            }
        }
    }

    /**
     * @param Field $intField
     * @param Field $objField
     * @return bool
     * @throws ConfigurationException
     */
    protected function assertFieldsIdentical($intField, $objField, $interface)
    {
        $intType = $intField->getConfig()->getType();
        $objType = $objField->getConfig()->getType();

        $isValid = true;
        if ($intType->getName() != $objType->getName()) {
            $isValid = false;
        }
        if ($intType->isCompositeType() && ($intType->getNamedType()->getName() != $objType->getNamedType()->getName())) {
            $isValid = false;
        }

        if (!$isValid) {
            throw new ConfigurationException(sprintf('Implementation of %s is invalid for the field %s', $interface->getName(), $objField->getName()));
        }
    }
}
