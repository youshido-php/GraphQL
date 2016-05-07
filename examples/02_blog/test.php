<?php

namespace BlogTest;

use Examples\Blog\Schema\PostType;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;

//require_once __DIR__ . '/../../vendor/autoload.php';
//require_once __DIR__ . '/Schema/PostType.php';       // including PostType definition

require_once __DIR__ .'/schema-bootstrap.php';

$rootQueryType = new ObjectType([
    'name' => 'RootQueryType',
    'fields' => [
        // you can specify fields for your query as an array
        'latestPost' => new PostType()
    ]
]);

$rootMutationType =  new ObjectType([
    'name'   => 'RootMutationType',
    'fields' => [
        'likePost' => [
            'type'    => new PostType(),
            'args'    => [
                'id' => new NonNullType(new IntType())
            ],
            'resolve' => function ($value, $args, $type) {
                // adding like count code goes here
                return [
                    'title' => 'Title for the post #' . $args['id'],
                    'summary' => 'We can now get a richer response from the mutation',
                    'likeCount' => 2
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
$payload  = 'mutation { likePost(id:5) }';

$processor->processRequest($payload);
echo json_encode($processor->getResponseData()) . "\n";
