<?php

namespace BlogTest;

use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Validator\SchemaValidator\SchemaValidator;


require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/inline-schema.php';
/** @var ObjectType $rootQueryType */

$schema = new Schema([
  'query' => $rootQueryType
]);

(new SchemaValidator())->validate($schema);

$processor = new Processor($schema);
$payload = '{ latestPost { title(truncated: true), summary } }';

$processor->processPayload($payload);
echo json_encode($processor->getResponseData()) . "\n";
