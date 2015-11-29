<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 3:53 PM
*/

namespace Youshido\GraphQL\Type\Config;


class SchemaConfig extends Config
{

    public function getRules()
    {
        return [
            'query' => ['type' => 'Object', 'required' => true],
            'mutation' => ['type' => 'Object'],
        ];
    }

}