<?php
/**
 * Date: 04.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition\Traits;


use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\TypeMap;

trait TypeCollectorTrait
{

    private $types = [];

    /**
     * @param $fields Field[]
     */
    private function collectTypes($fields)
    {
        foreach ($fields as $field) {
            if ($field->getConfig()->getType()->getKind() == TypeMap::KIND_LIST) {
                $name      = $field->getConfig()->getType()->getConfig()->getItem()->getName();
                $subFields = $field->getConfig()->getType()->getConfig()->getItem()->getConfig()->getFields();
                $type      = $field->getConfig()->getType()->getConfig()->getItem();
            } elseif ($field->getConfig()->getType() instanceof AbstractScalarType) {
                $name      = $field->getConfig()->getType()->getName();
                $type      = $field->getConfig()->getType();
                $subFields = [];
            } else {
                $name      = $field->getConfig()->getType()->getConfig()->getName();
                $subFields = $field->getConfig()->getType()->getConfig()->getFields();
                $type      = $field->getConfig()->getType();
            }

            if (!array_key_exists($name, $this->types)) {
                if ($name) {
                    $this->types[$name] = $type;
                }

                if ($subFields) {
                    $this->collectTypes($subFields);
                }
            }
        }
    }

}
