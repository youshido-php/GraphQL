<?php
namespace BlogTest;

use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;

require_once __DIR__ . '/../../vendor/autoload.php';
$rootQueryType = new ObjectType([
    'name' => 'RootQueryType',
]);

function resolvePost()
{
    return [
        "title"   => "Interesting approach",
        "summary" => "This new GraphQL library for PHP works really well",
    ];
}

require_once __DIR__ . '/structures/object-inline.php';

$processor = new Processor();
$processor->setSchema(new Schema([
    'query' => $rootQueryType
]));

$res = $processor->processRequest('{ latestPost { title, summary } }')->getResponseData();
echo json_encode($res);
echo "\n\n";
