<?php
/**
 * Date: 04.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition\Traits;


use Youshido\GraphQL\Type\Config\Field\FieldConfig;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeMap;

trait TypeCollectorTrait
{

    private $types = [];

    /**
     * @param $type TypeInterface
     */
    private function collectTypes($type)
    {
        switch ($type->getKind()) {
            case TypeMap::KIND_INTERFACE:
            case TypeMap::KIND_UNION:
            case TypeMap::KIND_ENUM:
            case TypeMap::KIND_SCALAR:
                $this->insertType($type->getName(), $type);
                break;

            case TypeMap::KIND_INPUT_OBJECT:
            case TypeMap::KIND_OBJECT:
                if ($type->getKind() == TypeMap::KIND_INPUT_OBJECT) {
                    $outputType = $type->getConfig()->getOutputType();

                    if ($outputType) {
                        $this->insertType($type->getConfig()->getOutputType()->getName(), $type->getConfig()->getOutputType());
                    }
                }

                if ($this->insertType($type->getName(), $type)) {
                    foreach ($type->getConfig()->getFields() as $field) {
                        /** @var FieldConfig $field */
                        $this->collectTypes($field->getType());
                    }
                    foreach ($type->getConfig()->getArguments() as $field) {
                        /** @var FieldConfig $field */
                        $this->collectTypes($field->getType());
                    }
                }
                break;

            case TypeMap::KIND_LIST:
                $subItem = $type->getConfig()->getItem();
                if ($this->insertType($subItem->getName(), $subItem)) {
                    foreach ($subItem->getConfig()->getFields() as $field) {
                        /** @var FieldConfig $field */
                        $this->collectTypes($field->getType());
                    }
                    foreach ($subItem->getConfig()->getArguments() as $field) {
                        /** @var FieldConfig $field */
                        $this->collectTypes($field->getType());
                    }
                }

                break;
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
