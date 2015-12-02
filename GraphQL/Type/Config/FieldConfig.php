<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/30/15 1:30 AM
*/

namespace Youshido\GraphQL\Type\Config;


use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeMap;

class FieldConfig extends Config
{

    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * @return TypeInterface
     */
    public function getType()
    {
        return $this->data['type'];
    }

    public function isRequired()
    {
        return !empty($this->data['required']) ? $this->data['required'] : false;
    }

    public function getRules()
    {
        return [
            'name'              => ['type' => TypeMap::TYPE_STRING, 'required' => true],
            'type'              => ['type' => TypeMap::TYPE_ANY, 'required' => true],
            'args'              => ['type' => 'array'],
            'required'          => ['type' => TypeMap::TYPE_BOOLEAN],
            'description'       => ['type' => TypeMap::TYPE_STRING],
            'resolve'           => ['type' => TypeMap::TYPE_FUNCTION],
            'deprecationReason' => ['type' => TypeMap::TYPE_STRING],
        ];
    }

}