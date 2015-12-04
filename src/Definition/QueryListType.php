<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Definition;


use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\ListType\AbstractListType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;

class QueryListType extends AbstractListType
{

    private $types = [];

    public function getName()
    {
        return '__TypeList';
    }

    public function getItem()
    {
        return new QueryType();
    }

    public function resolve($value = null, $args = [])
    {
        $this->types = [];

        /** @var $value Schema $a */
        $this->collectTypes($value->getQueryType()->getConfig()->getFields());

        return array_values($this->types);
    }

    /**
     * @param $fields Field[]
     */
    private function collectTypes($fields)
    {
        foreach ($fields as $field) {
            if ($field->getConfig()->getType() instanceof AbstractScalarType) {
                $name = $field->getConfig()->getType()->getName();
                $subFields = [];
            } else {
                $name = $field->getConfig()->getType()->getConfig()->getName();
                $subFields = $field->getConfig()->getType()->getConfig()->getFields();
            }

            if (!array_key_exists($name, $this->types)) {
                $this->types[$name] = $field->getConfig()->getType();

                if($subFields) {
                    $this->collectTypes($subFields);
                }
            }
        }
    }
}