<?php
namespace BlogTest;

use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\StringType;

$rootQueryType = new ObjectType([
    'name'   => 'RootQueryType',
    'fields' => [
        'latestPost' => new ObjectType([
            // you have to specify a string name
            'name'    => 'Post',
            // fields is an array of the array structure
            'fields'  => [
                'title'   => [
                    'type'              => new StringType(),
                    'description'       => 'This field contains a post title',
                    'isDeprecated'      => true,
                    'deprecationReason' => 'field title is now deprecated',
                    'args'              => [
                        'truncated' => [
                            'type' => new BooleanType()
                        ]
                    ],
                    'resolve'           => function ($value, $args) {
                        return (!empty($args['truncated'])) ? explode(' ', $value)[0] . '...' : $value;
                    }
                ],
                'summary' => new StringType(),
            ],
            'resolve' => function () {
                return [
                    "title"   => "Interesting approach",
                    "summary" => "This new GraphQL library for PHP works really well",
                ];
            }
        ])
    ]
]);
