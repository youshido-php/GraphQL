<?php
namespace Sandbox;

use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Validator\SchemaValidator\SchemaValidator;

require_once __DIR__ . '/../../vendor/autoload.php';

$schema = new Schema([
  'query' => new ObjectType([
    'name'   => 'RootQueryType',
    'fields' => [
      'currentTime' => [
        'type'    => new StringType(),
        'resolve' => function () {
          return date('Y-m-d H:ia');
        }
      ]
    ]
  ])
]);

(new SchemaValidator())->validate($schema);

$processor = new Processor($schema);

$processor->processPayload('{ currentTime }');
echo json_encode($processor->getResponseData()) . "\n";
