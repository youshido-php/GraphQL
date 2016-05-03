<?php

namespace BlogTest;

use Examples\Blog\Schema\PostType;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Schema/PostType.php';       // including PostType definition

$rootQueryType = new ObjectType([
    'name' => 'RootQueryType',
]);
$rootQueryType->addField('latestPost', new PostType());


$processor = new Processor();
$processor->setSchema(new Schema([
    'query'    => new ObjectType([
        'name'   => 'RootQueryType',
        'fields' => [
            'latestPost' => new PostType()
        ]
    ]),
    'mutation' => new ObjectType([
        'name'   => 'RootMutationType',
        'fields' => [
            'likePost' => [
                'type'    => new IntType(),
                'args'    => [
                    'id' => [
                        'type' => new IntType()
                    ]
                ],
                'resolve' => function () {
                    return 2;
                },
            ]
        ]
    ])
]));
$payload  = '{ latestPost { title, summary } }';
$payload  = 'mutation { likePost(id:5) }';
$response = $processor->processRequest($payload, [])->getResponseData();

echo json_encode($response) . "\n\n";
