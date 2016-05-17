<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:46 PM
*/

namespace Youshido\GraphQL\Relay\Field;


use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Relay\NodeInterface;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;

class NodeField extends AbstractField
{
    public function __construct()
    {
        $config = [
            'name'        => 'node',
            'description' => 'Fetches an object given its ID',
            'args'        => [
                'id' => [
                    'type'        => new NonNullType(new IdType()),
                    'description' => 'The ID of an object'
                ]
            ]
        ];
        parent::__construct($config);
    }

    public function getType()
    {
        return new NodeInterface();
    }

    public function resolve($value, $args = [], $type = null)
    {
        /** Need to find the best way to pass "TypeResolver" and "FetcherById/Type" here */
        return null;
    }


}
