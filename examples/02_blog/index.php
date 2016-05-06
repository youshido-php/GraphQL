<?php

namespace BlogTest;

use Examples\Blog\Schema\BlogSchema;
use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;

require_once __DIR__ . '/schema-bootstrap.php';
/** @var Schema $schema */
$schema = new BlogSchema();

$processor = new Processor();

$processor->setSchema($schema);
$payload  = 'mutation { likePost(id:5) { title(truncated: false), status, likeCount } }';
$payload  = '{ latestPost { title, status, likeCount } }';
$payload  = '{ pageContent { title } }';
$payload  = '{ pageContentUnion { ... on Post { title, summary } ... on Banner { title, imageLink } } }';
$payload  = '{ pageContentInterface { title} }';
$response = $processor->processRequest($payload, [])->getResponseData();

echo json_encode($response) . "\n\n";
