<?php
namespace BlogTest;

header('Access-Control-Allow-Credentials: false', true);
header('Access-Control-Allow-Origin: *');
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    return;
}

use Youshido\GraphQL\Processor;
use Youshido\GraphQL\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Validator\ResolveValidator\ResolveValidator;

require_once __DIR__ . '/../../vendor/autoload.php';
$rootQueryType = new ObjectType([
    'name' => 'RootQueryType',
]);

require_once __DIR__ . '/structures/object.php';
/**
 * Other implementations:
 * require_once __DIR__ . '/structures/object-inline.php';
 * require_once __DIR__ . '/structures/inline.php';
 */

if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
    $rawBody     = file_get_contents('php://input');
    $requestData = json_decode($rawBody ?: '', true);
} else {
    $requestData = $_POST;
}

$payload   = isset($requestData['query']) ? $requestData['query'] : null;
$variables = isset($requestData['variables']) ? $requestData['variables'] : null;


$processor = new Processor();
$processor->setSchema(new Schema([
    'query' => $rootQueryType
]));
$response = $processor->processRequest($payload, $variables)->getResponseData();

header('Content-Type: application/json');
echo json_encode($response);
