<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 3:53 PM
*/

namespace Youshido\GraphQL\Type\Config;


use Youshido\GraphQL\Type\Object\ObjectType;

class SchemaConfig extends Config
{

    /**
     * @return ObjectType
     */
    public function getQuery()
    {
        return $this->data['query'];
    }

    public function getMutation()
    {
        return !empty($this->data['mutation']) ? $this->data['mutation'] : null;
    }

    public function getRules()
    {
        return [
            'query' => ['type' => 'Object', 'required' => true],
            'mutation' => ['type' => 'Object'],
        ];
    }

}