<?php
/**
 * Date: 04.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection\Traits;

use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\CompositeTypeInterface;
use Youshido\GraphQL\Type\Config\Field\FieldConfig;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeMap;

trait TypeCollectorTrait
{

    protected $types = [];

    /**
     * @param $type TypeInterface
     */
    protected function collectTypes($type)
    {
        if (!$type) {
            return;
        }

        switch ($type->getKind()) {
            case TypeMap::KIND_INTERFACE:
            case TypeMap::KIND_UNION:
            case TypeMap::KIND_ENUM:
            case TypeMap::KIND_SCALAR:
                $this->insertType($type->getName(), $type);

                if ($type->getKind() == TypeMap::KIND_UNION) {
                    foreach ($type->getTypes() as $subType) {
                        if ($this->insertType($subType->getName(), $subType)) {
                            $this->collectFieldsArgsTypes($subType);

                            $this->checkAndInsertInterfaces($subType);
                        }
                    }
                }

                break;

            case TypeMap::KIND_INPUT_OBJECT:
            case TypeMap::KIND_OBJECT:
                if ($type->getKind() == TypeMap::KIND_INPUT_OBJECT) {
                    $outputType = $type->getConfig()->getOutputType();

                    if ($outputType) {
                        if ($this->insertType($outputType->getName(), $outputType)) {
                            $this->collectFieldsArgsTypes($outputType);
                        }
                    }
                } else {
                    $this->checkAndInsertInterfaces($type);
                }

                if ($this->insertType($type->getName(), $type)) {
                    $this->collectFieldsArgsTypes($type);
                }
                break;

            case TypeMap::KIND_LIST:
                $subItem = $type->getConfig()->getItem();
                if ($this->insertType($subItem->getName(), $subItem)) {
                    $this->collectFieldsArgsTypes($subItem);
                    $this->collectTypes($subItem);
                }

                foreach ($type->getConfig()->getArguments() as $argument) {
                    $this->insertType($argument->getConfig()->getType()->getName(), $argument->getConfig()->getType());
                }

                break;

            case TypeMap::KIND_NON_NULL:
                /** @var CompositeTypeInterface $type */
                if ($this->insertType($type->getTypeOf()->getName(), $type->getTypeOf())) {
                    $this->collectTypes($type->getTypeOf());
                }

                break;
        }
    }

    private function checkAndInsertInterfaces($type)
    {
        $interfaces = $type->getConfig()->getInterfaces();

        if (is_array($interfaces) && $interfaces) {
            foreach ($interfaces as $interface) {
                if ($this->insertType($interface->getName(), $interface)) {
                    $this->collectFieldsArgsTypes($interface);
                }
            }
        }
    }

    /**
     * @param $type AbstractType
     */
    private function collectFieldsArgsTypes($type)
    {
        foreach ($type->getConfig()->getFields() as $field) {
            $this->collectTypes($field->getType());
        }
        foreach ($type->getConfig()->getArguments() as $field) {
            /** @var FieldConfig $field */
            $this->collectTypes($field->getType());
        }
    }

    private function insertType($name, $type)
    {
        if (!array_key_exists($name, $this->types)) {
            $this->types[$name] = $type;

            return true;
        }

        return false;
    }

}
