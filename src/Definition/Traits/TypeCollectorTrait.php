<?php
/**
 * Date: 04.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition\Traits;


use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;

trait TypeCollectorTrait
{

    private $types = [];

    /**
     * @param $fields Field[]
     */
    private function collectTypes($fields)
    {
        foreach ($fields as $field) {
            if ($field->getConfig()->getType() instanceof AbstractScalarType) {
                $name      = $field->getConfig()->getType()->getName();
                $subFields = [];
            } else {
                $name      = $field->getConfig()->getType()->getConfig()->getName();
                $subFields = $field->getConfig()->getType()->getConfig()->getFields();
            }

            if (!array_key_exists($name, $this->types)) {
                $this->types[$name] = $field->getConfig()->getType();

                if ($subFields) {
                    $this->collectTypes($subFields);
                }
            }
        }
    }

}