<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 9:52 AM 5/15/16
 */

namespace Youshido\GraphQL\Field;


use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;

class FieldFactory
{
    public static function fromTypeWithResolver($name, AbstractType $type, $resolver = null)
    {
        return new Field([
            'name' => $name,
            'type' => $type,
            'args' => $type instanceof AbstractObjectType ? $type->getArguments() : [],
            'resolve' => $resolver ? $resolver : [$type, 'resolve'],
        ]);
    }
}