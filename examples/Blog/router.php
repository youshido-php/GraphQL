<?php
namespace BlogTest;
header('Access-Control-Allow-Credentials: false', true);
header('Access-Control-Allow-Origin: *');
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    die();
}

/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 8:21 PM 4/28/16
 */

use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Config\TypeConfigInterface;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IdType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;
use Youshido\Tests\Type\Union\Schema\QueryType;

require_once __DIR__ . '/../../vendor/autoload.php';
$rootQueryType = new ObjectType([
    'name' => 'RootQueryType',
]);

function resolvePost()
{
    return [
        "title"   => "Interesting approach",
        "summary" => "This new GraphQL library for PHP works really well",
        "status"  => null,
    ];
}

require_once __DIR__ . '/structures/object.php';
//require_once __DIR__ . '/structures/object-inline.php';
//require_once __DIR__ . '/structures/inline.php';


$processor = new Processor(new ResolveValidator());
$processor->setSchema(new Schema([
    'query' => $rootQueryType
]));


if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
    $rawBody = file_get_contents('php://input');
    $data    = json_decode($rawBody ?: '', true);
} else {
    $data = $_POST;
}

$query     = isset($data['query']) ? $data['query'] : null;
$variables = isset($data['variables']) ? $data['variables'] : null;


$res = $processor->processQuery($query, $variables)->getResponseData();
header('Content-Type: application/json');

$res = json_encode($res);
//$res = str_replace('"name":""', '"name": null', $res);
echo $res;