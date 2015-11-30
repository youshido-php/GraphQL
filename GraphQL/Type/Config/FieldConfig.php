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

    public function getRules()
    {
        return [
            'name' => ['type' => 'string', 'required' => true],
            'type' => ['required' => true],
            'args' => ['type' => 'array'],
            'description' => ['type' => 'string'],
            'resolve' => ['type' => 'callable'],
            'deprecationReason' => ['type' => 'string'],
        ];
    }

}