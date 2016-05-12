<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:46 PM
*/

namespace Youshido\GraphQL\Relay\Field;


use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Relay\NodeInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;

class NodeField extends Field
{
    public function __construct()
    {
        $config = [
            'name'        => 'node',
            'description' => 'Fetches an object given its ID',
            'type'        => new NodeInterface(),
            'args'        => [
                'id' => [
                    'type'        => new NonNullType(new IdType()),
                    'description' => 'The ID of an object'
                ]
            ]
        ];
        parent::__construct($config);
    }

    public function resolve($value, $args, $type)
    {
        /** Need to find the best way to pass "TypeResolver" and "FetcherById/Type" here */
        return null;
    }


}
