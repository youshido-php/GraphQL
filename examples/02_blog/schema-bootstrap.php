<?php

namespace BlogTest;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Schema/DataProvider.php';
require_once __DIR__ . '/Schema/PostType.php';
require_once __DIR__ . '/Schema/PostStatus.php';
require_once __DIR__ . '/Schema/ContentBlockInterface.php';
require_once __DIR__ . '/Schema/LikePostField.php';
require_once __DIR__ . '/Schema/BannerType.php';
require_once __DIR__ . '/Schema/ContentBlockUnion.php';
require_once __DIR__ . '/Schema/PostInputType.php';
require_once __DIR__ . '/Schema/BlogSchema.php';

/**
$rootQueryType = new ObjectType([
    'name'   => 'RootQueryType',
    'fields' => [
        'latestPost' => new PostType(),
        'randomBanner' => [
            'type' => new BannerType(),
            'resolve' => function() {
                return DataProvider::getBanner(rand(1, 10));
            }
        ],
        'pageContentUnion' => [
            'name'  => 'PageContentUnion',
            'type'  => new ListType(new ContentBlockUnion()),
            'resolve' => function() {
                return [DataProvider::getPost(1), DataProvider::getBanner(1)];
            }
        ],
        'pageContentInterface' => [
            'name'  => 'PageContentInterface',
            'type'  => new ListType(new ContentBlockInterface()),
            'resolve' => function() {
                return [DataProvider::getPost(2), DataProvider::getBanner(3)];
            }
        ],
    ]
]);

$rootMutationType = new ObjectType([
    'name'   => 'RootMutationType',
    'fields' => [
        'likePost' => new LikePost()
    ]
]);

$schema = new Schema([
    'query'    => $rootQueryType,
    'mutation' => $rootMutationType,
]);
*/
