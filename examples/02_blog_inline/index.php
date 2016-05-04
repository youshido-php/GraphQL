<?php

namespace BlogTest;

use Examples\Blog\Schema\PostType;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;


require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/inline-schema.php';
/** @var ObjectType $rootQueryType */


//$rootQueryType = new ObjectType([
//    'name'   => 'RootQuery',
//    'fields' => [
//        'latestPost' => [
//            'type' => 'Post',
//            'fields' => [
//                'title' => new StringType(),
//                'summary' => new StringType(),
//            ],
//            'resolve' => function() {
//                return [
//                    'title' => 'hello',
//                    'summary' => 'this is a post'
//                ];
//            }
//        ]
//    ]
//]);


$processor = new Processor();
$processor->setSchema(new Schema([
    'query' => $rootQueryType
]));
$payload  = '{ latestPost { title(truncated: true), summary } }';
$response = $processor->processRequest($payload, [])->getResponseData();

echo json_encode($response) . "\n\n";
