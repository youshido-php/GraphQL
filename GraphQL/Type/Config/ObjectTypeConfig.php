<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:32 AM
*/

namespace Youshido\GraphQL\Type\Config;


use Youshido\GraphQL\Type\Scalar\StringType;

class ObjectTypeConfig extends TypeConfig
{

    public function getRules()
    {
        return [
            'name'  => ['type' => new StringType()]
        ];
    }


}