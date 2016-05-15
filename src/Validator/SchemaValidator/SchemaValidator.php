<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 4/30/16 9:11 PM
*/

namespace Youshido\GraphQL\Validator\SchemaValidator;


use Youshido\GraphQL\Schema\AbstractSchema;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\InterfaceType\InterfaceType;
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
            if (!$schema->getQueryType()->hasFields()) {
                throw new ConfigurationException('Schema has to have fields');
            }
            foreach ($schema->getQueryType()->getConfig()->getFields() as $field) {
                if ($field->getType() instanceof AbstractObjectType) {
                    $this->assertInterfaceImplementationCorrect($field->getType());
                }
            }
        } catch (ConfigurationException $e) {
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
     * @param AbstractInterfaceType $interface
     * @return bool
     * @throws ConfigurationException
     */
    protected function assertFieldsIdentical($intField, $objField, AbstractInterfaceType $interface)
    {

        $isValid = true;
        if ($intField->getType()->isCompositeType() !== $objField->getType()->isCompositeType()) {
            $isValid = false;
        }
        if ($intField->getType()->getNamedType()->getName() != $objField->getType()->getNamedType()->getName()) {
            $isValid = false;
        }

        if (!$isValid) {
            throw new ConfigurationException(sprintf('Implementation of %s is invalid for the field %s', $interface->getName(), $objField->getName()));
        }
    }
}
