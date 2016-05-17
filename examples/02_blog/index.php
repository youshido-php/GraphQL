<?php

namespace BlogTest;

use Examples\Blog\Schema\BlogSchema;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema\Schema;

require_once __DIR__ . '/schema-bootstrap.php';
/** @var Schema $schema */
$schema = new BlogSchema();

$processor = new Processor();

$processor->setSchema($schema);
$payload = 'mutation { likePost(id:5) { title(truncated: false), status, likeCount } }';
$payload = '{ latestPost { title, status, likeCount } }';
$payload = '{ pageContent { title } }';
$payload = '{ pageContentUnion { ... on Post { title, summary } ... on Banner { title, imageLink } } }';
$payload = '{ pageContentInterface { title} }';
$payload = 'mutation { createPost(author: "Alex", post: {title: "Hey, this is my new post", summary: "my post" }) { title } }';

$processor->processRequest($payload);
echo json_encode($processor->getResponseData()) . "\n";
