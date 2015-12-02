<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:32 AM
*/

namespace Youshido\GraphQL\Type\Config\Object;


use Youshido\GraphQL\Type\Config\Config;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Field\Field;
use Youshido\GraphQL\Type\Config\Traits\ArgumentsAwareTrait;
use Youshido\GraphQL\Type\Config\Traits\FieldsAwareTrait;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;

class ScalarTypeConfig extends ObjectTypeConfig
{

    public function getRules()
    {
        $rules = parent::getRules();

        unset($rules['name']);

        return $rules;
    }
}