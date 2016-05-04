<?php
namespace BlogTest;

header('Access-Control-Allow-Credentials: false', true);
header('Access-Control-Allow-Origin: *');
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    return;
}

use Examples\Blog\Schema\LikePostMutationObject;
use Examples\Blog\Schema\PostType;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Schema/PostType.php';
require_once __DIR__ . '/Schema/LikePostMutation.php';

if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
    $rawBody     = file_get_contents('php://input');
    $requestData = json_decode($rawBody ?: '', true);
} else {
    $requestData = $_POST;
}

$payload   = isset($requestData['query']) ? $requestData['query'] : null;
$variables = isset($requestData['variables']) ? $requestData['variables'] : null;

$postType      = new PostType();
$rootQueryType = new ObjectType([
    'name'   => 'RootQueryType',
    'fields' => [
        'latestPost' => $postType
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
//                // increase like count
//                return $type->resolve($value, $args);
//            },
//        ]
        'likePost' => new LikePostMutationObject()
    ]
]);

$processor = new Processor();
$processor->setSchema(new Schema([
    'query'    => $rootQueryType,
    'mutation' => $rootMutationType,
]));

$response = $processor->processRequest($payload, $variables)->getResponseData();

header('Content-Type: application/json');
echo json_encode($response);
