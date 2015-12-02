<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 3:53 PM
*/

namespace Youshido\GraphQL\Type\Config\Schema;


use Youshido\GraphQL\Type\Config\Config;
use Youshido\GraphQL\Type\Object\InputObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\TypeMap;

class SchemaConfig extends Config
{

    public function getRules()
    {
        return [
            'query'    => ['type' => TypeMap::TYPE_OBJECT_TYPE, 'required' => true],
            'mutation' => ['type' => TypeMap::TYPE_OBJECT_TYPE],
        ];
    }

    /**
     * @return ObjectType
     */
    public function getQuery()
    {
        return $this->data['query'];
    }

    /**
     * @return InputObjectType
     */
    public function getMutation()
    {
        return  $this->get('mutation');
    }


}