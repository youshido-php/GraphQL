<?php
namespace BlogTest;

/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 4/28/16 11:31 PM
*/
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

$rootQueryType = new ObjectType([
    'name'   => 'RootQueryType',
    'fields' => [
        'latestPost' => new ObjectType([
            'name'    => 'Post',
            'fields'  => [
                'title'   => new StringType(),
                'summary' => new StringType(),
            ],
            'resolve' => function () {
                return resolvePost();
            }
        ])
    ]
]);