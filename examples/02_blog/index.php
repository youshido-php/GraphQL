<?php

namespace BlogTest;

use Examples\Blog\Schema\LikePost;
use Examples\Blog\Schema\PostType;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Schema/PostType.php';
require_once __DIR__ . '/Schema/LikePost.php';

$rootQueryType = new ObjectType([
    'name'   => 'RootQueryType',
    'fields' => [
        'latestPost' => new PostType()
    ]
]);

$rootMutationType = new ObjectType([
    'name'   => 'RootMutationType',
    'fields' => [
//        'likePost' => [
//            'type'    => new PostType(),
//            'args'    => [
//                'id' => new NonNullType(new IntType())
//            ],
//            'resolve' => function ($value, $args, $type) {
//                /** increase like count + 1 */
//
//                return $type->resolve($value, $args);
//            },
//        ]
        'likePost' => new LikePost()
    ]
]);

$processor = new Processor();

$processor->setSchema(new Schema([
    'query'    => $rootQueryType,
    'mutation' => $rootMutationType,
]));
$payload  = 'mutation { likePost(id:5) { title(truncated: false), likeCount } }';
$response = $processor->processRequest($payload, [])->getResponseData();

echo json_encode($response) . "\n\n";
