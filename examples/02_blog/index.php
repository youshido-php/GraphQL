<?php

namespace BlogTest;

use Examples\Blog\Schema\PostType;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/Schema/PostType.php';       // including PostType definition

$rootQueryType = new ObjectType([
    'name' => 'RootQueryType',
]);
$rootQueryType->getConfig()->addField('latestPost', new PostType());

$processor = new Processor();
$processor->setSchema(new Schema([
    'query' => $rootQueryType
]));
$payload = '{ latestPost { title, summary } }';
$response = $processor->processRequest($payload, [])->getResponseData();

print_r($response);
