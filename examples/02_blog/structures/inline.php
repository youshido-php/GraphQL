<?php
namespace BlogTest;

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
                return [
                    "title"   => "Interesting approach",
                    "summary" => "This new GraphQL library for PHP works really well",
                    "status"  => null,
                ];
            }
        ])
    ]
]);