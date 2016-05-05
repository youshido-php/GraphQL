<?php

namespace BlogTest;

use Examples\Blog\Schema\BannerType;
use Examples\Blog\Schema\BlockContentUnion;
use Examples\Blog\Schema\LikePost;
use Examples\Blog\Schema\PostType;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Object\UnionType;
use Youshido\GraphQL\Type\Scalar\IntType;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Schema/PostType.php';
require_once __DIR__ . '/Schema/PostStatus.php';
require_once __DIR__ . '/Schema/ContentBlockInterface.php';
require_once __DIR__ . '/Schema/LikePost.php';
require_once __DIR__ . '/Schema/BannerType.php';
require_once __DIR__ . '/Schema/BlockContentUnion.php';

$rootQueryType = new ObjectType([
    'name'   => 'RootQueryType',
    'fields' => [
        'latestPost' => new PostType(),
        'randomBanner' => new BannerType(),
        'pageContent' => [
            'name'  => 'PageContent',
            'type'  => new ListType(new BlockContentUnion()),
            'resolve' => function() {
                return [
                    'title' => 'block content title'
                ];
            }
        ],
        'BlockContentUnion' => new BlockContentUnion()
    ]
]);

$rootMutationType = new ObjectType([
    'name'   => 'RootMutationType',
    'fields' => [
        'likePost' => new LikePost()
    ]
]);

$processor = new Processor();

$processor->setSchema(new Schema([
    'query'    => $rootQueryType,
    'mutation' => $rootMutationType,
]));
$payload  = 'mutation { likePost(id:5) { title(truncated: false), status, likeCount } }';
$payload  = '{ latestPost { title, status, likeCount } }';
$payload  = '{ pageContent { title } }';
$response = $processor->processRequest($payload, [])->getResponseData();

echo json_encode($response) . "\n\n";
