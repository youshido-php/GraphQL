<?php

namespace BlogTest;

use Examples\Blog\Schema\LikePostMutation;
use Examples\Blog\Schema\PostType;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Schema/PostType.php';
require_once __DIR__ . '/Schema/LikePostMutation.php';

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
        'likePost' => new LikePostMutation()
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
