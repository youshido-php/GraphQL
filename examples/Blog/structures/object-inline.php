<?php

namespace BlogTest;

use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;

$rootQueryType = new ObjectType([
    'name' => 'RootQueryType',
]);
$rootQueryType->getConfig()->addField('latestPost',
    new ObjectType([
        'name'   => 'Post',
        'fields' => [
            'title'   => new StringType(),
            'summary' => new StringType(),
        ],
        'args'   => [
            'id' => new IdType(),
        ],
    ]),
    [
        'resolve' => function () {
            return [
                "title"   => "Interesting approach",
                "summary" => "This new GraphQL library for PHP works really well",
                "status"  => null,
            ];
        }
    ]);