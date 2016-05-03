<?php

namespace BlogTest;

use Examples\Blog\Schema\PostType;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Schema/PostType.php';       // including PostType definition

$postType = new PostType();
$rootQueryType = new ObjectType([
    'name' => 'RootQueryType',
    'fields' => [
        'latestPost' => $postType
    ]
]);

$rootMutationType =  new ObjectType([
    'name'   => 'RootMutationType',
    'fields' => [
        'likePost' => [
            'type'    => $postType,
            'args'    => [
                'id' => [
                    'type' => new NonNullType(new IntType())
                ]
            ],
            'resolve' => function ($value, $args, $type) {
                return $type->resolve($value, $args, $type);
                return [
                    'title' => 'Title for the post #' . $args['id'],
                    'summary' => 'We can now get a richer response from the mutation',
                    'likeCount' => 3
                ];
            },
        ]
    ]
]);

$processor = new Processor();

$processor->setSchema(new Schema([
    'query'    => $rootQueryType,
    'mutation' => $rootMutationType,
]));
$payload  = 'mutation { likePost(id:5) { title, likeCount } }';
$response = $processor->processRequest($payload, [])->getResponseData();

echo json_encode($response) . "\n\n";
